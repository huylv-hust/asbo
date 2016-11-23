<?php

namespace Repair;

class Controller_Summary extends \Controller_Usappy
{

	/**
	 * Index
	 * @author Y.Hasegawa
	 * @since 1.0.0
	 * @param
	 * @return
	*/
	public function action_index()
	{
		$this->template->title = 'Usappyオートサービス管理';

		$year = (int)date('Y');
		$month = (int)date('m') - 1;

		$months = array();

		for ($i=0;$i<2;$i++)
		{
			$months[] = array(
				'year' => $year,
				'month' => $month
			);
			$month++;
			if ($month > 12)
			{
				$month -= 12;
				$year++;
			}
		}

		$model = new \Model_Repaireventplan();

		$this->template->content = \View::forge('summary/index', array(
			'months' => $months
		));
	}

	public function get_downloadevent()
	{
		$model = new \Model_Repaireventplan();
		$sum = $model->sum(
			\Fuel\Core\Input::get('branch_code'),
			\Fuel\Core\Input::get('event_id'),
			\Fuel\Core\Input::get('repair_staff_id')
		);

		$lines = array();
		$lines[] = array(
			'支店平均', '売上計（税抜）','台数計（台）','見積誤差','キャンセル台数','残業時間',
			'①レパレート','②ピース当たり時間(分）','③施工稼働率','④ピース当たり単価',
			'⑤予約率','⑥台単価','⑦コスト率'
		);

		$denominator = $sum['staff_count'] == 0 ? 1.0 : (float)$sum['staff_count'];

		$lines[] = array(
			'',
			(float)$sum['price'] / $denominator,
			(float)$sum['car_count'] / $denominator,
			(float)$sum['piece_count_diff'] / $denominator,
			(float)$sum['cancel_count'] / $denominator,
			(float)$sum['over_work_min'] / $denominator / 60.0,
			(float)$sum['reparate'] / $denominator,
			(float)$sum['work_time_per_piece'] / $denominator,
			sprintf('%0.2f', $sum['work_rate'] * 100.0),
			(float)$sum['price_per_piece'] / $denominator,
			sprintf('%0.2f', $sum['reservation_rate'] * 100.0),
			(float)$sum['price_per_car'] / $denominator,
			sprintf('%0.2f', $sum['cost_rate'] * 100.0)
		);

		$lines[] = array(
			'支店計', '売上計（税抜）','台数計（台）','見積誤差','キャンセル台数','残業時間'
		);

		$lines[] = array(
			'',
			$sum['price'],
			$sum['car_count'],
			$sum['piece_count_diff'],
			$sum['cancel_count'],
			(float)$sum['over_work_min'] / 60.0
		);

		$body = '';
		foreach ($lines as $line)
		{
			$body .= \Utility::make_csvline($line);
		}

		$csv_name = 'repair_report_event.csv';

		$response = new \Response($body, 200, array(
			'Content-disposition' => 'attachment; filename=' . $csv_name,
			'Content-type' => 'application/octet-stream; name=' . $csv_name,
			'Cache-Control' => 'public',
			'Pragma' => 'public'
		));

		return $response;
	}

	public function get_downloadmonth()
	{
		list($year, $month) = explode('-', \Fuel\Core\Input::get('month'));

		$model = new \Model_Repairreport();
		$sum = $model->sum_monthly(
			$year,
			$month,
			\Fuel\Core\Input::get('branch_code'),
			\Fuel\Core\Input::get('repair_staff_id')
		);

		$lines = array();
		$lines[] = array(
			'売上計（税抜）','台数計（台）','見積誤差','キャンセル台数','残業時間',
			'①レパレート','②ピース当たり時間(分）','③施工稼働率','④ピース当たり単価',
			'⑤予約率','⑥台単価','⑦コスト率'
		);

		$lines[] = array(
			$sum['price'],
			$sum['car_count'],
			$sum['piece_count_diff'],
			$sum['cancel_count'],
			(float)$sum['over_work_min'] / 60.0,
			$sum['reparate'],
			$sum['work_time_per_piece'],
			sprintf('%0.2f', $sum['work_rate'] * 100.0),
			$sum['price_per_piece'],
			sprintf('%0.2f', $sum['reservation_rate'] * 100.0),
			$sum['price_per_car'],
			sprintf('%0.2f', $sum['cost_rate'] * 100.0)
		);

		$body = '';
		foreach ($lines as $line)
		{
			$body .= \Utility::make_csvline($line);
		}

		$csv_name = 'repair_report_month.csv';

		$response = new \Response($body, 200, array(
			'Content-disposition' => 'attachment; filename=' . $csv_name,
			'Content-type' => 'application/octet-stream; name=' . $csv_name,
			'Cache-Control' => 'public',
			'Pragma' => 'public'
		));

		return $response;
	}
}
