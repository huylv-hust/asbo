<?php
namespace Fuel\Tasks;

class Report
{
	public static function run($year, $month)
	{

		$email = \Email::forge();
		$email->from('hasegawa@d-o-m.jp');
		$email->to('hasegawa@d-o-m.jp');
		$email->subject('as report');

		for ($i=0;$i<2;$i++)
		{
			$month += $i;
			if ($month > 12)
			{
				$month -= 12;
				$year++;
			}

			$ym = sprintf('%04d%02d', $year, $month);

			$sql = "
					select
						sscode,
						(
							select count(*) from reservation as r1 where ss.sscode = r1.sscode and r1.menu_code = 'oil' and
							date_format(r1.start_time, '%Y%m') = '$ym' and save_from = 'ss'
						) as oilbo,
						(
							select count(*) from reservation as r1 where ss.sscode = r1.sscode and r1.menu_code = 'oil' and
							date_format(r1.start_time, '%Y%m') = '$ym' and save_from = 'usappy'
						) as oilpub,
						(
							select count(*) from reservation as r1 where ss.sscode = r1.sscode and r1.menu_code = 'tire' and
							date_format(r1.start_time, '%Y%m') = '$ym' and save_from = 'ss'
						) as tirebo,
						(
							select count(*) from reservation as r1 where ss.sscode = r1.sscode and r1.menu_code = 'tire' and
							date_format(r1.start_time, '%Y%m') = '$ym' and save_from = 'usappy'
						) as tirepub,
						(
							select count(*) from reservation as r1 where ss.sscode = r1.sscode and r1.menu_code = 'inspection' and
							date_format(r1.start_time, '%Y%m') = '$ym' and save_from = 'ss'
						) as inspectionbo,
						(
							select count(*) from reservation as r1 where ss.sscode = r1.sscode and r1.menu_code = 'inspection' and
							date_format(r1.start_time, '%Y%m') = '$ym' and save_from = 'usappy'
						) as inspectionpub,
						(
							select count(*) from reservation as r1 where ss.sscode = r1.sscode and r1.menu_code = 'wash' and
							date_format(r1.start_time, '%Y%m') = '$ym' and save_from = 'ss'
						) as washbo,
						(
							select count(*) from reservation as r1 where ss.sscode = r1.sscode and r1.menu_code = 'wash' and
							date_format(r1.start_time, '%Y%m') = '$ym' and save_from = 'usappy'
						) as washpub,
						(
							select count(*) from reservation as r1 where ss.sscode = r1.sscode and r1.menu_code = 'coating' and
							date_format(r1.start_time, '%Y%m') = '$ym' and save_from = 'ss'
						) as coatingbo,
						(
							select count(*) from reservation as r1 where ss.sscode = r1.sscode and r1.menu_code = 'coating' and
							date_format(r1.start_time, '%Y%m') = '$ym' and save_from = 'usappy'
						) as coatingpub,
						(
							select count(*) from repair_reservation as r2 where ss.sscode = r2.sscode and
							date_format(r2.arrival_time, '%Y%m') = '$ym'
						) as repair
					from
					(
						select sscode from open_timer
						union
						select sscode from reservation
						union
						select sscode from repair_reservation
						group by sscode
					) as ss
				";

			$rows = \Fuel\Core\DB::query($sql)->execute()->as_array();

			$header = "会社	支店名	SS名	SSコード	管理側予約件数：オイル	公開側予約件数：オイル	管理側予約件数：タイヤ	公開側予約件数：タイヤ	管理側予約件数：車検	公開側予約件数：車検	管理側予約件数：洗車	公開側予約件数：洗車	管理側予約件数：コーティング	公開側予約件数：コーティング	管理側予約件数：リペア";

			$body = \Utility::make_csvline(explode("\t", $header));

			$allss = [];
			foreach (\Api::get_ss_name() as $ss) {
				$allss[$ss['sscode']] = $ss;
			}

			foreach ($rows as $row)
			{
				$ss = $allss[$row['sscode']];

				$company = ''; $_match = null;
				if (preg_match('/^(東日本|西日本|ダイツー)/', $ss['branch_name'], $_match))
				{
					$company = $_match[1];
				}

				$body .= \Utility::make_csvline([
					$company,
					$ss['branch_name'],
					$ss['ss_name'],
					$row['sscode'],
					$row['oilbo'],
					$row['oilpub'],
					$row['tirebo'],
					$row['tirepub'],
					$row['inspectionbo'],
					$row['inspectionpub'],
					$row['washbo'],
					$row['washpub'],
					$row['coatingbo'],
					$row['coatingpub'],
					$row['repair']
				]);
			}

			$email->string_attach($body, '作業予約件数('.$ym.').csv');
		}

		$email->send();
	}
}
