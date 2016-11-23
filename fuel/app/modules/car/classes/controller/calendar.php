<?php

namespace Car;
use \Model\Car;
use \Model\Carreservation;

class Controller_Calendar extends \Controller_Usappy
{

	/**
	 * Carlendar
	 * @author NamNT
	 * @since 1.0.0
	 * @param
	 * @return Carlendar
	*/
	public function action_index()
	{
		$car = new \Model_Car();
		$carreservation = new \Model_Carreservation;

		if(\Fuel\Core\Input::get('type_check') && \Fuel\Core\Cookie::get('info_cs_move_to_car') != '' && \Fuel\Core\Cookie::get('is_change_sscode') != '1')
		{
			$data_cookie = json_decode(\Fuel\Core\Cookie::get('info_cs_move_to_car'),true);
			$data['car_sscode'] = $data_cookie['sscode'];
			$_arr_ss_name = \Api::get_ss_name($data['car_sscode']);
			$data['car_sscodename'] = $_arr_ss_name['0']['ss_name'];
			\Fuel\Core\Cookie::delete('is_change_sscode');
		}
		else
		{
			$data = $this->create_cookie_ssinfo('car');
			\Fuel\Core\Cookie::delete('is_change_sscode');

		}
		$data['subnav'] = array('index' => 'active');
		$data['listCar'] = $car->get_car_list($data['car_sscode']);

		$this->template->title = 'Usappyオートサービス管理';
		$this->template->content = \View::forge('cars/calendar', $data);
		$this->template->content->ssfinder = \View::forge('partials/ssfinder');
	}
	/**
	 * Get Booking Data
	 * @author NamNT
	 * @since 1.0.0
	 * @param
	 * @return Booking data of a car
	*/
	public function action_get_booking_data()
	{
		$carreservation = new \Model_Carreservation;
		$sscode = \Cookie::get('sscode');
		$car_id = \Input::param('id');

		if($car_id)
		{
			\Cookie::set('car_id_book',$car_id, 60 * 60 * 24);
		}

		$datas = $carreservation->get_reservation_info($car_id);
		$content_type = array('Content-type' => 'application/json');
		return new \Response(json_encode($datas), 200, $content_type);
	}

	public function action_search_ss()
	{
		$branch = \Input::param('branch');
		$key = \Input::param('ssname');
		$data = \api::search($branch, $key);
		$content_type = array('Content-type' => 'application/json');
		return new \Response(json_encode($data), 200, $content_type);
	}

	public function action_set_cookie()
	{
		$car_sscode = \Input::param('sscode');
		$car_sscodename = \Input::param('ssname');
		$screen_name = \Input::param('screen_name');
		$pit_no = \Input::param('pit_no');
		if($screen_name == 'car')
		{
			\Fuel\Core\Cookie::set('is_change_sscode',1);
		}
		\Cookie::set('car_id_book',0, 60 * 60 * 24);
		$this->set_cookie_ssinfo($screen_name, $car_sscode, $car_sscodename);
		if($pit_no)
		{
			$pit = new \Model_Pit();
			$list = $pit->get_pit_list($car_sscode);
			$rs = \Constants::array_to_option($list,'pit_no','pit_name');
			$content_type = array();
			return new \Response($rs, 200, $content_type);
		}

		exit();
	}
}
