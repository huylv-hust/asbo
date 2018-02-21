<?php

namespace Repair;
use \Model\Repairreservation;

class Controller_Calendar extends \Controller_Usappy
{

	/**
	 * Index
	 * @author NamNT
	 * @since 1.0.0
	 * @param
	 * @return index
	*/
	public function action_index()
	{
		if(\Fuel\Core\Input::get('type_check') && \Fuel\Core\Cookie::get('info_cs_move_to_car') != '' && \Fuel\Core\Cookie::get('is_change_sscode') != '1')
		{
			$data_cookie = json_decode(\Fuel\Core\Cookie::get('car_data'),true);
			$data['repair_sscode'] = $data_cookie['car_info_sscode'];
			$_arr_ss_name = \Api::get_ss_name($data['repair_sscode']);
			$data['repair_sscodename'] = $_arr_ss_name['0']['ss_name'];
			\Fuel\Core\Cookie::delete('is_change_sscode');
		}
		else
		{
			$data = $this->create_cookie_ssinfo('repair');
			\Fuel\Core\Cookie::delete('is_change_sscode');
		}

		//set cookie return url
		\Cookie::set('repair_retun_url', \Uri::current().'?redirect=1', 60 * 60 * 24);
		$this->template->title = 'Usappyオートサービス管理';
		$this->template->content = \View::forge('calendar/index', $data);
		$this->template->content->ssfinder = \View::forge('partials/ssfinder');

	}
	/**
	 * Get Repair Reservation Data
	 * @author NamNT
	 * @since 1.0.0
	 * @param $sscode
	 * @return $datas
	*/
	public function action_get_booking_data()
	{
		$repair_reservation = new \Model_Repairreservation;
		$sscode = $this->get_cookie_sscode('repair_sscode');

		$datas = $repair_reservation->get_repair_reservation_list($sscode);
		$content_type = array('Content-type' => 'application/json');
		return new \Response(json_encode($datas), 200, $content_type);
	}

}
