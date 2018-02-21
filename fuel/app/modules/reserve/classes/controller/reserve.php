<?php

/**
 * Insert/Edit
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 19/05/2015
 */
namespace Reserve;

class Controller_Reserve extends \Controller_Usappy
{
	public static $data_cs_oracle = array();
	public static $data_cs_api = array();

	public function action_index()
	{
		$this->template->title = 'Usappyオートサービス管理';
		$reservation = new \Model_Reservation();
		$reservationno = new \Model_Reservationno();
		$cs = new \Model_Customer();
		$pit = new \Model_Pit();
		$reservation_no = \Input::get('reservation_no','');
		$reservation_no_reser = '';
		if($reservation_no)
		{
			$sscode = \Input::get('sscode');
		}
		else
		{
			$sscode = \Cookie::get('sscode');

		}
		$data = $reservation->set_default_data();
		$data['sscode'] = $sscode;
		$data['start_time'] = \Input::param('start');
		$data['end_time'] = \Input::param('end');
		$data['check_edit'] = 0;
		$row = $data;
		$is_found = true;
		if($reservation_no)
		{
			$row = $reservation->get_reservation_info($reservation_no);
			if(count($row))
			{
				$data = $row;
				if($row['hashkey'] === null)
				{
					$_array_cs = $cs->get_member_info($row,$reservation_no);
				}
				else
				{
					$_array_cs = $cs->get_oracle_member_info($reservation_no, $row);
				}

				$data = $data + $_array_cs;
			}
			else
			{
				$is_found = false;
				$data['reservation_delete'] = 'この予約は削除されました。変更の保存に失敗しました。';

			}

			$data['check_edit'] = 0;
			if($is_found && $row['sscode'] != \Cookie::get('sscode') && $row['menu_code'] !='inspection')
			{
				$data['check_edit'] = 1;
			}

		}

		$type_check = \Fuel\Core\Input::get('type_check',0); // get data cus for pre
		if($type_check > 0)
		{
			$data_prev = \Fuel\Core\Cookie::get('car_data');
			if($data_prev != '')
			{
				$data_prev = json_decode($data_prev, true);
				$data_cs = $data_prev['data_cs'];
				$_array_cs = array(
					'usappy_id'      => $data_prev['usappy_id'],
					'cs_card_number' => $data_prev['cs_card_no'],
					'cs_name'        => $data_cs['member_kaiinName'],
					'cs_name_kana'   => $data_cs['member_kaiinKana'],
					'cs_mobile_tel'  => $data_cs['member_telNo1'],
					'cs_house_tel'   => $data_cs['member_telNo2'],
				);
				$data = $data + $_array_cs;
				//$data['sscode'] = $data_prev['car_info_sscode'];
			}
		}

		if($is_found)
		{
			$list = $pit->get_pit_list($data['sscode']);
			$data['pit_no'] = \Constants::array_to_option($list,'pit_no','pit_name',$row['pit_no']);
			$data['is_car_request'] = \Constants::select('is_car_request_oil_tire_wash','is_car_request',$row['is_car_request'],array('class' => 'form-control'));

			$data['tire_size_code'] = \Constants::select('tire_size_code','',$row['tire_size_code'],array('class' => 'form-control'));
			$data['tire_preparation_code'] = \Constants::select('tire_preparation_code','',$row['tire_preparation_code'],array('class' => 'form-control'));
			$data['wheel_preparation_code'] = \Constants::select('wheel_preparation_code','',$row['wheel_preparation_code'],array('class' => 'form-control'));
			$data['pit_work'] = \Constants::select('pit_work','',$row['menu_code'],array('class' => 'form-control'));
			$data['car_size'] = \Constants::select('car_size','car_size_code',$row['car_size_code'],array('class' => 'form-control'));
			if($row['car_size_code'])
			{
				$data['car_weight'] = \Constants::select('car_weight_'.$row['car_size_code'],'car_weight_code',$row['car_weight_code'],array('class' => 'form-control'));
			}
			else
			{
				$data['car_weight'] = '<select id="form_car_weight_code" name="car_weight_code" class="form-control"><option value="0"></option></select>';
			}

			$data['car_color'] = \Constants::select('car_color','car_color_code',$row['car_color_code'],array('class' => 'form-control'));
			$data['coating_code'] = \Constants::select('coating_code','coating_code',$row['coating_code'],array('class' => 'form-control'));
			$data['is_shuttle_request'] = \Constants::select('is_shuttle_request','is_shuttle_request',$row['is_shuttle_request'],array('class' => 'form-control'));

			if(\Input::post() && $is_found == true)
			{
				$data_save = $this->action_get_data();
				if( ! $reservation_no)
				{
					$reservation_no = $reservationno->create_reservationo_no('W');
					$data_save['reservation_no'] = $reservation_no;
				}
				else
				{
					$reservation_no_reser = $reservation_no;
				}

				$old_info = $reservation->get_reservation_info($reservation_no);
				$hashkey = null;
				if(count($old_info))
				{
					$hashkey = $old_info['hashkey'];
				}

				if($hashkey === null)
				{
					$rs = $cs->save_user_info($reservation_no);
				}
				else
				{
					$rs = $cs->save_user_info($reservation_no,true);
				}

				if($rs >= 0)
				{
					$result = $reservation->reservation_save($data_save,$reservation_no_reser);
					if(count($result) == 0)
					{
						$data['reservation_delete'] = 'この予約は削除されました。変更の保存に失敗しました。';
					}
					else
					{
						if(\Input::post('savejson') == 1)
						{
							\Fuel\Core\Cookie::delete('info_cs_move_to_car');
							$data_save_cookie = array(
								'usappy_id'      => \Input::param('usappy_id'),
								'cs_name'        => \Input::param('cs_name'),
								'cs_name_kana'   => \Input::param('cs_name_kana'),
								'cs_mobile_tel'  => \Input::param('cs_mobile_tel'),
								'cs_house_tel'   => \Input::param('cs_house_tel'),
								'cs_card_number' => \Input::param('cs_card_number'),
								'sscode'         => \Input::param('sscode'),
							);
							\Cookie::set('info_cs_move_to_car', json_encode($data_save_cookie));
							\Response::redirect(\Uri::base().'car/calendar?type_check=1');
						}

						if(\Input::param('type'))
						{
							\Response::redirect(\Uri::base());
						}

						\Fuel\Core\Cookie::set('reserve_calendar_url_redirect',$data_save['start_time']);
						if($data_save['arrival_time'])
							\Fuel\Core\Cookie::set('reserve_calendar_url_redirect',$data_save['arrival_time']);

						if(\Input::param('pos') == '1')
						{
							if(\Session::get('url_redirect'))
							{
								\Response::redirect(\Session::get('url_redirect'));
							}

							\Response::redirect(\Uri::base().'reserve/list');
						}

						if(\Input::param('pos') == '-1')
						{
							\Response::redirect(\Uri::base());
						}

						\Response::redirect(\Uri::base().'reserve/calendar?redirect=1');
					}
				}
				else
				{
					$data['error'] = 'データの保存に失敗しました。';
				}
			}
		}
		$this->template->content = \View::forge('reserve/index', $data);
		$this->template->content->carselect = \Presenter::forge('car/select')->set('obj',$row);
		$this->template->content->ssfinder = \View::forge('partials/ssfinder');

	}

	/**
	 * get data
	 * @return array
	 */
	protected function action_get_data()
	{
		$from_date_hh = \Input::param('from_date_hh');
		$from_date_mm = \Input::param('from_date_mm');
		$to_date_hh = \Input::param('to_date_hh');
		$to_date_mm = \Input::param('to_date_mm');
		$arrival_time_hh = \Input::param('arrival_time_hh','00');
		$arrival_time_mm = \Input::param('arrival_time_mm' ,'00');

		$from_date = \Input::param('from_date').' '.str_pad($from_date_hh,2,STR_PAD_LEFT).':'.str_pad($from_date_mm,2,STR_PAD_LEFT).':00';
		$to_date = \Input::param('to_date').' '.str_pad($to_date_hh,2,STR_PAD_LEFT).':'.str_pad($to_date_mm,2,STR_PAD_LEFT).':00';
		$arrival_time = null;
		if(\Input::param('arrival_time'))
		{
			$arrival_time = \Input::param('arrival_time').' '.str_pad($arrival_time_hh,2,STR_PAD_LEFT).':'.str_pad($arrival_time_mm,2,STR_PAD_LEFT).':00';
		}

		$sscode = \Input::param('sscode','');
		$usappy_id = \Input::param('usappy_id');
		$card_no = \Input::param('cs_card_number');
		/* Info reservation */
		$pit_work = \Input::param('pit_work');// menu_code
		$menu_name = \Input::param('menu_name',null);
		$pit_no = \Input::param('pit_no',null);
		$plate_no	= \Input::param('plate_no');
		$car_maker_code = \Input::param('car_maker_code', null);
		$car_grade_code = \Input::param('car_grade_code', null);
		$car_model_code = \Input::param('car_model_code' , null);
		$car_color_code = \Input::param('car_color_code', null);
		$coating_code = \Input::param('coating_code');
		$car_size_code = \Input::param('car_size_code' , null);
		$car_weight_code = \Input::param('car_weight_code' ,null);
		$inspection_date = \Input::param('inspection_date' ,null);
		$is_car_request = \Input::param('is_car_request', null);
		$other_request = \Input::param('other_request', null);
		$tire_size_code = \Input::param('tire_size_code');
		$tire_preparation_code = \Input::param('tire_preparation_code');
		$wheel_preparation_code = \Input::param('wheel_preparation_code');

		$car_year = \Input::param('car_year');
		$car_month = \Input::param('car_month');
		$car_type_code = \Input::param('car_type_code');
		$data = array(
			'sscode'                 => $sscode,
			'menu_code'              => $pit_work,
			'menu_name'              => $menu_name,
			'pit_no'                 => $pit_no,
			'arrival_time'           => $arrival_time,
			'start_time'             => $from_date,
			'end_time'               => $to_date,
			'member_id'	             => $usappy_id,
			'card_no'                => $card_no,
			'car_maker_code'         => $car_maker_code,
			'car_model_code'         => $car_model_code,
			'plate_no'               => $plate_no,
			'car_year'               => $car_year,
			'car_month'              => $car_month,
			'car_type_code'          => $car_type_code,
			'car_grade_code'         => $car_grade_code,
			'car_color_code'         => $car_color_code,
			'coating_code'           => $coating_code,
			'car_size_code'          => $car_size_code,
			'car_weight_code'        => $car_weight_code,
			'inspection_date'        => $inspection_date,
			'is_car_request'         => $is_car_request,
			'tire_size_code'         => $tire_size_code,
			'wheel_preparation_code' => $wheel_preparation_code,
			'tire_preparation_code'  => $tire_preparation_code,
			'state'                  => '0',
			'other_request'          => $other_request,
			'is_shuttle_request'     => \Fuel\Core\Input::param('is_shuttle_request'),
		);

		foreach($data as $k => $v)
		{
			if($v === '')
			{
				$data[$k] = null;
			}

			continue;
		}

		return $data;
	}
}
