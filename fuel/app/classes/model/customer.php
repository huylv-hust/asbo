<?php
/**
 * Customer class
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 12/05/2015
 */

class Model_Customer
{
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $card_no
	 * @param type $birthday
	 * @return boolean
	 */
	public function search($card_no, $birthday)
	{

		if($card_no == '' or $birthday == '')
		{
			return false;
		}

		$info_card = API::get_info_card($card_no);

		if($info_card['result'] == 1 && $info_card['member_birthday'] == $birthday)
		{
			return $info_card;
		}

		elseif($info_card['result'] == 3)
		{
			$info_card['error'] = 'カード番号が正しくありません';
			return $info_card;
		}

		elseif($info_card['result'] == 500)
		{
			$info_card['error'] = 'エラーが発生しました';
			return $info_card;
		}

		else
		{
			$info_card['result'] = -1;
			$info_card['error'] = '生年月日が正しくありません';
			return $info_card;
		}


	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $usappy_id
	 * @param type $reservation_no
	 * @return boolean
	 */
	public function get_member_info( $car_reservation_info,$reservation_no = '')
	{
		$_array_cs = array();
		$usappy_id = $car_reservation_info['member_id'];
		if($usappy_id == '' && $reservation_no == '')
		{
			return false;
		}

		if($usappy_id)
		{
			$cs_info = Api::get_member_base_info($usappy_id);
			$_array_cs = array(
				'usappy_id'      => $cs_info['member_kaiinCd'],
				'cs_card_number' => $car_reservation_info['card_no'],
				'cs_name'        => $cs_info['member_kaiinName'],
				'cs_name_kana'   => $cs_info['member_kaiinKana'],
				'cs_mobile_tel'  => $cs_info['member_telNo1'],
				'cs_house_tel'   => $cs_info['member_telNo2'],
			);
			return $_array_cs;
		}

		$cs_info = Model_Customeroracle::get_member_info_oracle($reservation_no);
		if(count($cs_info))
		{
			$cs_info = $cs_info['0'];
			$_array_cs = array(
				'usappy_id'      => '',
				'cs_card_number' => $car_reservation_info['card_no'],
				'cs_name'        => $cs_info['customer_name'],
				'cs_name_kana'   => $cs_info['customer_kana'],
				'cs_mobile_tel'  => $cs_info['mobile_tel'],
				'cs_house_tel'   => $cs_info['house_tel'],
			);
		}

		return $_array_cs;
	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $data
	 * @param type $card_no
	 * @param type $reservation_no
	 * @return boolean
	 */
	public function save($data,$usappy_id,$reservation_no)
	{

		if(($usappy_id == '' && $reservation_no == '') or ! count($data))
		{
			return false;
		}

		if($usappy_id)
		{
			return Api::update_member_basic($usappy_id,$data);
		}

		$cs_oracle = new Model_Customeroracle();
		return $cs_oracle->customer_save($data,$reservation_no);
	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $list
	 * @return array
	 */
	public function get_list_member_info($list)
	{
		$usappy_id = '';
		$reservation_no = '';
		$list_cs_oracle = array();
		$list_cs_api = array();
		$result = array();
		foreach ($list as $_temp)
		{
			if($_temp['member_id'])
			{
				$usappy_id .= $_temp['member_id'].',';
			}
			$reservation_no .= $_temp['reservation_no'].',';
		}

		$usappy_id = trim($usappy_id,',');
		if($usappy_id)
		{
			$list_cs_api = \Api::get_members($usappy_id);
			if (is_array($list_cs_api) == false)
			{
				$list_cs_api = array();
			}
		}

		if($reservation_no)
		{
			$cs_oracle = new Model_Customeroracle();
			$list_cs_oracle = $cs_oracle->get_list_members($reservation_no);
		}

		return $list_cs_oracle + $list_cs_api;

	}
	public function save_user_info($reservation_no,$save_oracle = false)
	{

		if(\Input::param('usappy_id') && $save_oracle == false)
		{
			$data_cs_api = array(
			 'member_kaiinName' => \Input::param('cs_name',''),
			 'member_kaiinKana' => \Input::param('cs_name_kana'),
			 'member_telNo1'    => \Input::param('cs_mobile_tel'),
			 'member_telNo2'    => \Input::param('cs_house_tel'),
			);
			return Api::update_member_basic(\Input::param('usappy_id'),$data_cs_api);
		}

		/* Infor Cus*/
		$data_cs_oracle = array(
		 'customer_name' => \Input::param('cs_name',''),
		 'customer_kana' => \Input::param('cs_name_kana'),
		 'mobile_tel'    => \Input::param('cs_mobile_tel'),
		 'house_tel'     => \Input::param('cs_house_tel'),
		 'updated_at'    => \DB::expr('current_date'),
		);
		$cs_oracle = new Model_Customeroracle();
		return $cs_oracle->customer_save($data_cs_oracle,$reservation_no);

	}

	/**
	 *
	 * @param type $reservation_no
	 * @return type
	 */
	public function get_oracle_member_info( $reservation_no, $car_reservation_info)
	{
		$_array_cs = array();
		$cs_info = Model_Customeroracle::get_member_info_oracle($reservation_no);
		if(count($cs_info))
		{
			$cs_info = $cs_info['0'];
			$_array_cs = array(
				'usappy_id'      => '',
				'cs_card_number' => $car_reservation_info['card_no'],
				'cs_name'        => $cs_info['customer_name'],
				'cs_name_kana'   => $cs_info['customer_kana'],
				'cs_mobile_tel'  => $cs_info['mobile_tel'],
				'cs_house_tel'   => $cs_info['house_tel'],
			);
		}

		return $_array_cs;
	}

}
