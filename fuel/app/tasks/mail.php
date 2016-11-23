<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Fuel\Tasks;
use Api;
use Constants;
use Utility;
class Mail
{
	public static function run($speech = null)
	{
		$cus = new \Model_Customer();
		$sql = 'SELECT '
			. 'reservation_no,'
			. 'sscode,'
			. 'menu_code,'
			. 'menu_name,'
			. 'arrival_time,'
			. 'start_time,'
			. 'end_time,'
			. 'member_id,'
			. 'hashkey '
			. 'FROM reservation '
			. 'WHERE start_time >= DATE_ADD(CURDATE(),INTERVAL 3 DAY) AND start_time < DATE_ADD(CURDATE(),INTERVAL 4 DAY)';
			;
		$result = \Fuel\Core\DB::query($sql)->execute();


		if( ! count($result))
		{
			return;
		}

		$cusInfo = new \Model_Customeroracle();
		foreach($result as $data)
		{

			if($data['hashkey'] === null){
				$cs_info = \Api::get_member_base_info($data['member_id']);
			}
			else
			{
				/*No Login No member*/
				$cs_info = $cusInfo->get_member_info_oracle($data['reservation_no']);
				$cs_info = Utility::convert_customer_info_oracel($cs_info);

			}

			#TODO neu ko co du lieu
			if( ! count($cs_info))
			{
				continue;
			}

			$data['user'] = array(
				'member_kaiinName'    => $cs_info['member_kaiinName'],
				'member_kaiinKana'    => $cs_info['member_kaiinKana'],
				'member_telNo1'       => $cs_info['member_telNo1'],
				'member_telNo2'       => $cs_info['member_telNo2'],
				'member_mailAddress1' => $cs_info['member_mailAddress1'],
				'member_mailAddress2' => $cs_info['member_mailAddress2'],
			);



			$data['ss_info'] = current(\Api::search_ss(array('sscode' => $data['sscode'])));
			$subject = 'Usappy【'.\Constants::$pit_work[$data['menu_code']].'】来店３日前です。';
			if($data['menu_code'] == 'other')
				$subject = 'Usappy【'.$data['menu_name'].'】来店３日前です。';

			$mail_to = array();
			if($data['user']['member_mailAddress1'])
			{
				$mail_to[$data['user']['member_mailAddress1']] = $data['user']['member_kaiinName'];
			}

			if($data['user']['member_mailAddress2'])
			{
				$mail_to[$data['user']['member_mailAddress2']] = $data['user']['member_kaiinName'];
			}

			$data['hostname'] = \Config::get('hostname');
			if ($data['hostname'] == null)
			{
				$data['hostname'] = 'usappy.jp';
			}

			if (count($mail_to) == 0)
			{
				continue;
			}
			Utility::sendmail($mail_to, $subject, $data,'mail/index_cron_email');

		}

	}
}
