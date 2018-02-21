<?php

/**
 * List function
 *
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 2/06/2015
 */
namespace Ajax;
class Controller_Common extends \Controller_Usappy
{
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * get info car
	 * @return \Response
	 */
	public function action_car()
	{
		$maker_code = \Input::param('car_maker_code','');
		$model_code = \Input::param('car_model_code','');
		$year = \Input::param('car_year','');
		$type_code = \Input::param('car_type_code','');
		$level = \Input::param('level','');
		if($maker_code && $model_code && $year && $type_code && $level == 4)
		{
			$list_grade_code = \Api::get_list_grade_code($maker_code,$model_code,$year,$type_code);

			$option = str_replace('<option value="0"></option>','<option value="0">グレードを選択して下さい</option>',\Constants::array_to_option($list_grade_code,'grade_code','grade'));
			return new \Response($option, 200,array());

		}

		if($maker_code && $model_code && $year && $level == 3)
		{
			$list_type_code = \Api::get_list_type_code($maker_code,$model_code,$year);
			$option = str_replace('<option value="0"></option>','<option value="0">型式を選択して下さい</option>',\Constants::array_to_option($list_type_code,'type_code','type'));
			return new \Response($option,200,array());
		}

		if($maker_code && $model_code && $level == 2)
		{
			$list_year = \Api::get_list_year_month($maker_code,$model_code);
			$option = '<option value="0">初度登録年を選択して下さい</option>';
			if( ! isset($list_year['result']))
			{
				$option = str_replace('<option value="0"></option>','<option value="0">初度登録年を選択して下さい</option>',\Constants::array_to_option($list_year,'year','year'));
			}

			return new \Response($option, 200,array());
		}

		if($maker_code && $level == 1)
		{

			$list_model_code = \Api::get_list_model($maker_code);
			$option = str_replace('<option value="0"></option>','<option value="0">モデルを選択して下さい</option>',\Constants::array_to_option($list_model_code,'model_code','model'));
			return new \Response($option, 200,array());
		}
	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * search ss
	 * @return \Response
	 */
	public function action_ss_search()
	{
		$sscode = \Input::param('sscode');
		if($sscode)
		{
			$ss = \Api::get_ss_name($sscode);
			if(count($ss))
			{
				return new \Response(1, 200, array());
			}

			return new \Response(0, 200, array());
		}

		return new \Response(0, 200, array());
	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * get pit
	 * @return \Response
	 */
	public function action_get_pit()
	{
		$sscode = \Input::param('sscode','');
		if($sscode)
		{
			$pit = new \Model_Pit();
			$list = $pit->get_pit_list($sscode);
			return new \Response(\Constants::array_to_option($list,'pit_no','pit_name'), 200, array());
		}
	}

	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * get card info
	 */
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
			return new \Response($rs, 200, array());
		}
		else
		{
			$rs['error'] = $card_info['error'];
			return new \Response(json_encode($rs), 200, array());
		}
	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * get staff
	 * @return \Response
	 */
	public function action_get_staff()
	{
		$staff = new \Model_Repairestaff();
		$branch = \Input::param('branch');
		$config['where'][] = array(
			'branch_code',
			'=',
			$branch,
		);
		$list = $staff->search_repair_staff_list($config);

		return new \Response(\Constants::array_to_option($list,'repair_staff_id','staff_name','',false), 200, array());

	}
	public function action_weight()
	{
		$weight	= \Input::param('weight');
		$weight = 'car_weight_'.$weight;
		$arr_weight = \Constants::$$weight;
		return new \Response(\Constants::to_option($arr_weight), 200, array());

	}
	public function action_setcookie()
	{
		$start_time	= str_replace('/','-',\Input::param('start_time'));
		$name_cookie = \Fuel\Core\Input::param('name_cookie');
		\Fuel\Core\Cookie::set($name_cookie.'_url_redirect',$start_time);
		return new \Response(1, 200, array());

	}

	public function get_repair_events()
	{
		$model = new \Model_Repaireventplan();
		return new \Response(json_encode($model->get_events(\Input::param('branch_code'))), 200, array());
	}

	public function get_ss()
	{
		$sscode = \Input::param('sscode');

		$ss = null;

		if($sscode)
		{
			$list = \Api::get_ss_name($sscode);
			if (count($list) > 0)
			{
				$ss = array_shift($list);
			}
		}

		return new \Response(json_encode($ss), 200, array());
	}

}
