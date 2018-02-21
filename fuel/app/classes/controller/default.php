<?php
use \Model\Reservation;
use \Model\Repairreservation;

/**
 * Reserve class
 *
 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
 * @date 06/05/2015
 */
class Controller_Default extends Controller_Template
{
	/*
	 * Index action
	 *
	 * @since 07/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_index()
	{
		$this->template->title = 'Usappyオートサービス管理';

		$sscode = \Input::param('sscode');
		if($sscode)
		{
			$data['api'] = Api::get_ss_name($sscode);
			//check response sscode
			if ( ! $data['api'])
			{
				\Response::redirect(Fuel\Core\Uri::base().'sss');
			}

			Cookie::set('sscode', $data['api'][0]['sscode'], 60 * 60 * 24);
			Cookie::set('ss_name', $data['api'][0]['ss_name'], 60 * 60 * 24);
			//head - css, js
			$this->template->head = View::forge('partials/head');
			//navigator
			$this->template->navigator = View::forge('partials/navi', $data);
			//footer - footer of page
			$this->template->footer = View::forge('partials/footer');

			$this->template->content = $this::forge('default/index', $data);
		}
		else
		{
			if( ! Cookie::get('sscode'))
			{
				\Response::redirect(Fuel\Core\Uri::base().'sss');
			}

			//head - css, js
			$this->template->head = View::forge('partials/head');
			//navigator
			$this->template->navigator = View::forge('partials/navi');
			//footer - footer of page
			$this->template->footer = View::forge('partials/footer');
			$this->template->content = $this::forge('default/index');
		}

	}

	public function action_delcode()
	{
		\Cookie::delete('sscode');
		\Cookie::delete('ss_name');
		return false;
	}
	/*
	 * Get Booking Data
	 *
	 * @since 28/05/2015
	 * @author NamNT
	 */
	public function action_get_booking_data()
	{
		$reservation = new \Model_reservation;
		$repair_reservation = new \Model_Repairreservation;

		$data_reservation1 = $reservation->get_reservation_list_by_day(Cookie::get('sscode'));
		$data_reservation2 = $reservation->get_reservation_list_arrival_time_by_day(Cookie::get('sscode'));
		$data_reservation  = array_merge($data_reservation1,$data_reservation2);

		$data_repair_reservation = $repair_reservation->get_repair_reservation_list_by_day(Cookie::get('sscode'));
		$datas = array_merge($data_reservation,$data_repair_reservation);
		$content_type = array('Content-type' => 'application/json');
		return new \Response(json_encode($datas), 200, $content_type);
	}

	public function action_sss()
	{
		\Fuel\Core\Response::redirect('http://'.\Fuel\Core\Input::server('SERVER_NAME').'/sss/');
	}
}
