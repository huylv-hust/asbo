<?php
/**
 * carreservation class
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 12/05/2015
 */
namespace Car;
class Controller_Reserve extends \Controller_Usappy
{

	/**
	 * Car reserve
	 * @author NamDD
	 * @since 1.0.0
	 * @param
	 * @return Reserve
	 */
	public function action_index()
	{
		$this->template->title = 'Usappyオートサービス管理';
		$car_ob = new \Model_Car();
		$car_reservation = new \Model_Carreservation();
		$customer = new \Model_Customer();
		$data['ss_name'] = \Cookie::get('ss_name');
		$data['sscode'] = \Cookie::get('sscode');
		$reservation_no = \Input::param('reservation_no','');
		$cs_info = array();
		$data['cs_info'] = $cs_info;
		if(\Fuel\Core\Cookie::get('info_cs_move_to_car') != '' && \Fuel\Core\Input::get('type_check'))
		{
			$data_cookie = json_decode(\Fuel\Core\Cookie::get('info_cs_move_to_car'),true);
			/*
			$data['sscode'] = $data_cookie['sscode'];
			$_arr_ss_name = \Api::get_ss_name($data['sscode']);
			$data['ss_name'] = $_arr_ss_name['0']['ss_name'];
			 *
			 */
			$data['cs_info'] = $data_cookie;


		}

		if($reservation_no)
		{
			$car_reservation_info = $car_reservation->get_car_reservation_info($reservation_no);
			if(count($car_reservation_info))
			{
				$car_reservation_info = $car_reservation_info->to_array();
				$data['sscode'] = $car_reservation_info['sscode'];
				$data['purpose_code'] = $car_reservation_info['purpose_code'];
				$ss_name = \Api::get_ss_name($car_reservation_info['sscode']);
				if(count($ss_name))
				{
					$data['ss_name'] = $ss_name['0']['ss_name'];
				}
				else
				{
					\Response::redirect_back(\Uri::base().'car/calendar');
				}
			}
			else
			{
				\Response::redirect_back(\Uri::base().'car/calendar');
			}

			$time_start = $car_reservation_info['start_time'];
			$time_end   = $car_reservation_info['end_time'];
			$car_id = $car_reservation_info['car_id'];
			$cs_info = $customer->get_member_info($car_reservation_info,$reservation_no);
			/*set info cs if edit*/
			$data['cs_info'] = $cs_info;
		}
		else
		{
			$time_start = \Input::param('start');
			$time_end   = \Input::param('end');
			$car_id = \Cookie::get('car_id_book');
		}

		if($car_id == 0)
		{
			\Response::redirect_back(\Uri::base().'car/calendar');
		}

		if($time_start)
		{
			$data['date_start'] = $time_start;
		}

		if($time_end)
		{
			$data['date_end'] = $time_end;
		}

		$data['url_base'] = \Fuel\Core\Uri::base();
		/*get sscode & ssname  & set data*/
		$car_info = $car_ob->get_car_info($car_id);
		if(count($car_info))
		{
			$ss_car = \API::get_ss_name($car_info['sscode']);
			$car_info['ss_name'] = $ss_car['0']['ss_name'];
			$data['car_info'] = $car_info;
		}
		else
		{
			\Response::redirect_back(\Uri::base().'car/calendar');
		}

		/*set data edit*/
		$data['reservation_no'] = $reservation_no;
		$data['car_id'] = $car_id;


		if(\Input::post())
		{
			if( ! $this->action_save($car_id,$reservation_no));
			{
				$ms = '入力頂いた時間帯は既に予約済みです。別の代車を予約するか、別の時間帯をお選び下さい。';
				$data['error'] = $ms;
			}
		}

		$this->template->content = \View::forge('reserve/index', $data);
		$this->template->content->ssfinder = \View::forge('partials/ssfinder');

	}
	public function action_save($car_id,$reservation_no)
	{
		$usappy_id	 = \Input::param('usappy_id');
		$cs_card_no	= \Input::param('cs_card_number');
		$purpose_code	= \Input::param('purpose_code') == '' ? null : \Input::param('purpose_code');
		$cs_name	   = \Input::param('cs_name');
		$cs_name_kana  = \Input::param('cs_name_kana');
		$cs_mobile_tel = \Input::param('cs_mobile_tel');
		$cs_house_tel  = \Input::param('cs_house_tel');
		$ss_code = \Cookie::get('sscode');
		$start_time = \Input::param('from_date').' '.str_pad(\Input::param('from_date_hh'),2,'0',STR_PAD_LEFT).':'.str_pad(\Input::param('from_date_mm'),2,'0',STR_PAD_LEFT).':00';
		$end_time = \Input::param('to_date').' '.str_pad(\Input::param('to_date_hh'),2,'0',STR_PAD_LEFT).':'.str_pad(\Input::param('to_date_mm'),2,'0',STR_PAD_LEFT).':00';
		$type = \Input::param('type');

		$list_reservation_car = \DB::select('*')->from('car_reservation')->where('car_id', $car_id)->and_where('reservation_no','!=',$reservation_no)->execute()->as_array();
		$is_add = true;
		foreach($list_reservation_car as $row)
		{
			if(($row['start_time'] < $start_time && $start_time < $row['end_time']) || ($row['start_time'] < $end_time && $end_time < $row['end_time']))
			{
				$is_add = false;
				break;
			}

			if($start_time <= $row['start_time'] && $end_time >= $row['end_time'])
			{
				$is_add = false;
				break;
			}
		}

		if($is_add)
		{
			$data_cs = array(
				'member_kaiinName' => $cs_name,
				'member_kaiinKana' => $cs_name_kana,
				'member_telNo1'    => $cs_mobile_tel,
				'member_telNo2'    => $cs_house_tel,
			);
			$data_car_reservationo = array(
				'car_id'       => $car_id,
				'start_time'   => $start_time,
				'end_time'     => $end_time,
				'sscode'       => $ss_code,
				'member_id'    => $usappy_id,
				'card_no'      => $cs_card_no,
				'purpose_code' => $purpose_code,
				'updated_at'   => date('Y-m-d H:i:s',time()),
			);
			$data_cs_oracle = array(
				'customer_name' => $cs_name,
				'customer_kana' => $cs_name_kana,
				'mobile_tel'    => $cs_mobile_tel,
				'house_tel'     => $cs_house_tel,
				'updated_at'    => \DB::expr('current_date'),
			);

			if( ! $reservation_no)
			{
				$data_car_reservationo['created_at'] = date('Y-m-d H:i:s',time());
				$data_cs_oracle['created_at'] = \DB::expr('current_date');
			}

			$data['data_cs'] = $data_cs;
			$data['data_car_reservationo'] = $data_car_reservationo;
			$data['data_cs_oracle'] = $data_cs_oracle;
			$data['usappy_id'] = $usappy_id;
			$data['cs_card_no'] = $cs_card_no;
			$data['reservation_no'] = $reservation_no;
			$data['sscode'] = $ss_code;
			$ucar = new \Model_Ucar();

			$reserve = $ucar->reserve($data);
			if($type != 0)
			{
				$data['car_info_sscode'] = \Fuel\Core\Input::param('car_info_sscode');
				\Fuel\Core\Cookie::set('car_data',json_encode($data));
			}

			if($reserve)
			{
				\Fuel\Core\Cookie::set('car_calendar_url_redirect',$start_time);
				//\Fuel\Core\Response::redirect ('car/calendar?redirect=1');
				if($type == 1)
				{
					\Fuel\Core\Response::redirect ('reserve/calendar/?type_check=1');
				}

				elseif($type == 2)
				{
					\Fuel\Core\Response::redirect ('repair/calendar/?type_check=2');
				}

				else
				{
					\Fuel\Core\Response::redirect ('car/calendar?redirect=1');
				}
			}
			else
			{
				\Fuel\Core\Response::redirect ('car/reserve/?start='.\Input::param('start').'&end='.\Input::param('end'));
			}
		}
		else
		{
			return false;
		}
	}
	public function action_getcardinfo()
	{
		$cs = new \Model_Customer();
		$card_no	= \Input::param('card_no');
		$birthday	= \Input::param('birthday');
		$card_info  = $cs->search($card_no,$birthday);
		if($card_info['result'] == '1') // Get Susses
		{
			$card_info['card_no'] = $card_no;
			$card_info['error'] = '0';
			$rs = json_encode($card_info);
			echo $rs;
			die;
		}
		else
		{
			$rs['error'] = $card_info['error'];
			echo json_encode($rs);
			die;
		}
	}
	public function action_delete()
	{
		$car_reservation = new \Model_Carreservation();
		$customer_oracle = new \Model_Customeroracle();
		$reservation_no = \Input::param('reservation_no','');
		$rs = $car_reservation->car_reservation_delete($reservation_no);
		if($rs)
		{
			$customer_oracle->customer_delete($reservation_no);
			\Fuel\Core\Response::redirect ('car/calendar/');
			echo '0k';
		}
		else
		{
			echo 'Error Delete';
		}

		die;
	}
}
