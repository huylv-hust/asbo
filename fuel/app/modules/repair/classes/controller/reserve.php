<?php

/**
 * Insert/Edit class
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 19/05/2015
 */
namespace Repair;

class Controller_Reserve extends \Controller_Usappy
{


	public function action_index()
	{
		$this->template->title = 'Usappyオートサービス管理';
		$reservation = new \Model_Repairreservation();
		$reservationno = new \Model_Reservationno();
		$staff = new \Model_Repairestaff();
		$cs = new \Model_Customer();
		$reservation_no = \Input::get('reservation_no','');
		$reservation_no_repair = '';
		//$sscode = \Input::param('sscode');
		if($reservation_no)
		{
			$sscode = \Input::param('sscode');
		}
		else
		{
			$sscode = \Cookie::get('sscode');

		}

		$staff_info = array();
		$list_staff = array();
		$data = $reservation->set_default_data();
		$data['check_edit'] = 0;
		$data['sscode'] = $sscode;
		$data['arrival_time'] = \Input::param('date');
		$branch_df = 0;

		$ss_info = \Api::get_ss_name(\Cookie::get('sscode'));
		$branch_df = $ss_info[0]['branch_code'];


		$row = $data;

		$is_found = true;
		if($reservation_no)
		{
			$row = $reservation->get_repair_reservation_info($reservation_no);
			if(count($row))
			{
				$data = $row;
				$_array_cs = $cs->get_member_info($row,$reservation_no);
				$data = $data + $_array_cs;
				$config['where'][] = array(
					'repair_staff_id',
					'=',
					$row['repair_staff_id'],
				);

				$staff_info = $staff->search_repair_staff_list($config);
				$staff_info = current($staff_info);
				$config_list['where'][] = array(
					'branch_code',
					'=',
					$staff_info['branch_code'],
				);
				$list_staff = $staff->search_repair_staff_list($config_list);

				if ($data['image_keys_json'] !== null)
				{
					$data['image_keys'] = json_decode($data['image_keys_json'], true);
				}
			}
			else
			{
				$is_found = false;
				$data['reservation_delete'] = 'この予約は削除されました。変更の保存に失敗しました。';
			}

			$sscode = $data['sscode'];
			$data['check_edit'] = 0;
			if($is_found && $row['sscode'] != \Cookie::get('sscode'))
			{
				$data['check_edit'] = 1;
			}
		}
		else
		{
			$sscode = \Cookie::get('sscode');
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

		$branch = array('' => '未定');
		foreach (\Constants::$branch as $branch_code => $branch_name)
		{
			$branch[$branch_code] = $branch_name;
		}

		if($is_found)
		{
			$data['branch'] = \Constants::array_to_select($branch, '', '', 'branch', ((isset($staff_info['branch_code'])) ? $staff_info['branch_code'] : $branch_df),array('class' => 'form-control'));
			$data['list_staff'] = \Constants::array_to_option($list_staff,'repair_staff_id','staff_name',$row['repair_staff_id'],false);
			$data['car_color'] = \Constants::select('car_color','car_color_code',$row['car_color_code'],array('class' => 'form-control'));
			$data['is_shuttle_request'] = \Constants::select('is_shuttle_request','is_shuttle_request',$row['is_shuttle_request'],array('class' => 'form-control'));
			if(\Input::post())
			{
				$data_save = $this->action_get_data();

				if( ! $reservation_no) // Add New
				{
					$reservation_no = $reservationno->create_reservationo_no('R');
					$data_save['reservation_no'] = $reservation_no;
				}
				else
				{
					$reservation_no_repair = $reservation_no;
				}

				$rs = $cs->save_user_info($reservation_no);
				if($rs >= 0)
				{
					$reservation->repair_reservation_save($data_save,$reservation_no_repair);
					$pos = \Input::param('pos');
					if(\Input::param('type'))
					{
						\Response::redirect(\Uri::base());
					}

					if(\Input::post('savejson') == 1)
					{
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

					if($pos == '1')
					{
						if(\Session::get('url_redirect_repair'))
						{
							\Response::redirect(\Session::get('url_redirect_repair'));
						}

						\Response::redirect(\Uri::base().'repair/list');
					}

					if(\Input::param('pos') == '-1')
					{
						\Response::redirect(\Uri::base());
					}
					
					\Fuel\Core\Cookie::set('repair_calendar_url_redirect',$data_save['arrival_time']);
					\Response::redirect(\Uri::base().'repair/calendar?redirect=1');
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

	protected function action_get_data()
	{
		$return_time = null;
		if(\Input::param('return_time'))
		{
			$return_time = \Input::param('return_time').' '.\Input::param('return_time_hh','00').':'.\Input::param('return_time_mm','00');
		}

		$arrival_time = \Input::param('arrival_time').' '.\Input::param('arrival_time_hh','00').':'.\Input::param('arrival_time_mm' ,'00');

		$sscode = \Input::param('sscode','');
		$usappy_id = \Input::param('usappy_id');
		$card_no = \Input::param('cs_card_number');
		/* Info reser */
		$a_piece_count	= \Input::param('a_piece_count');
		$b_piece_count = \Input::param('b_piece_count');
		$repair_staff_id = \Input::param('repair_staff_id');
		$plate_no	= \Input::param('plate_no');
		$price = \Input::param('price');
		$car_maker_code = \Input::param('car_maker_code');
		$car_grade_code = \Input::param('car_grade_code');
		$car_model_code = \Input::param('car_model_code');
		$car_year = \Input::param('car_year');
		$car_month = \Input::param('car_month');
		$color_number = \Input::param('color_number');
		$car_color_code = \Input::param('car_color_code', null);
		$car_type_code = \Input::param('car_type_code');
		$image_keys = \Input::param('image_keys', null);
		if($image_keys != null)
		{
			$image_keys_json = json_encode($image_keys);
		}
		else
		{
			$image_keys_json = null;
		}

		$updated_at = date('Y-m-d H:i',time());
		$data = array(
			'sscode'             => $sscode,
			'arrival_time'       => $arrival_time,
			'return_time'        => $return_time,
			'a_piece_count'      => $a_piece_count,
			'b_piece_count'      => $b_piece_count,
			'member_id'          => $usappy_id,
			'card_no'            => $card_no,
			'repair_staff_id'    => $repair_staff_id,
			'car_maker_code'     => $car_maker_code,
			'car_model_code'     => $car_model_code,
			'plate_no'           => $plate_no,
			'price'              => $price,
			'car_year'           => $car_year,
			'car_month'          => $car_month,
			'car_type_code'      => $car_type_code,
			'car_grade_code'     => $car_grade_code,
			'car_color_code'     => $car_color_code,
			'color_number'       => $color_number,
			'image_keys_json'    => $image_keys_json,
			'state'              => '0',
			'updated_at'         => $updated_at,
			'is_car_request'     => \Input::param('is_car_request'),
			'is_shuttle_request' => \Fuel\Core\Input::param('is_shuttle_request'),
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
