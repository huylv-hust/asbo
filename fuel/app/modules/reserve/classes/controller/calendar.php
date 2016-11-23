<?php

namespace Reserve;
use \Model\Reservation;

class Controller_Calendar extends \Controller_Usappy
{

	/**
	 * List Car
	 * @author NamNT
	 * @since 1.0.0
	 * @param
	 * @return List Car
	*/
	public function action_index()
	{

		if(\Fuel\Core\Input::get('type_check') && \Fuel\Core\Cookie::get('info_cs_move_to_car') != '' && \Fuel\Core\Cookie::get('is_change_sscode') != '1')
		{
			$data_cookie = json_decode(\Fuel\Core\Cookie::get('car_data'),true);
			$data['reserve_sscode'] = $data_cookie['car_info_sscode'];
			$_arr_ss_name = \Api::get_ss_name($data['reserve_sscode']);
			$data['reserve_sscodename'] = $_arr_ss_name['0']['ss_name'];
			\Fuel\Core\Cookie::delete('is_change_sscode');
		}
		else
		{
			$data = $this->create_cookie_ssinfo('reserve');
			\Fuel\Core\Cookie::delete('is_change_sscode');
		}

		$reservation = new \Model_Reservation;
		$sscode = $this->get_cookie_sscode('reserve_sscode');
		$data['events'] = array_merge(
			$reservation->get_reservation_list($sscode),
			$reservation->get_reservation_list_by_arrival_time($sscode)
		);

		//set cookie return url
		\Cookie::set('reserve_return_url', \Uri::current().'?redirect=1', 60 * 60 * 24);

		$this->template->title = 'Usappyオートサービス管理';
		$this->template->content = \View::forge('calendar/index',$data);
		$this->template->content->ssfinder = \View::forge('partials/ssfinder');
	}

	public function action_get_booking_data()
	{
		$reservation = new \Model_Reservation;

		$car_id = \Input::param('id');

		$sscode = $this->get_cookie_sscode('reserve_sscode');

		$data1  = $reservation->get_reservation_list($sscode);
		$datas2 = $reservation->get_reservation_list_by_arrival_time($sscode);
		$datas  = array_merge($data1,$datas2);

		$content_type = array('Content-type' => 'application/json');
		return new \Response(json_encode($datas), 200, $content_type);
	}

}
