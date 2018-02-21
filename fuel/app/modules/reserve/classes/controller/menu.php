<?php

/**
 * Menu setting class
 *
 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
 * @date 20/05/2015
 */

namespace Reserve;

class Controller_Menu extends \Controller_Usappy
{
	/*
	 * Index action
	 *
	 * @since 20/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_index()
	{
		$this->template->title = 'Usappyオートサービス管理 - Reserve menu';

		//If is submit
		if (\Input::method() == 'POST') 
		{
			$data = \Input::post();
			//validate 受付時間枠(平日)
			$validate_week = \Model_Menusetting::validate_startenddate($data, 'week');
			if($validate_week == 'errors')
			{
				return 1;
			}
			else
			{
				$dayinweek_date_start = $validate_week['start'];
				$dayinweek_date_end   = $validate_week['end'];
			}
			
			// 他に時間枠と重複があります
			if(\Model_Menusetting::validate_date($dayinweek_date_start, $dayinweek_date_end) == 'false')
			{
				return 11;
			}
			
			//validate 受付時間枠(土日祝祭日)
			$validate_holiday = \Model_Menusetting::validate_startenddate($data, 'holiday');
			if($validate_holiday == 'errors')
			{
				return 2;
			}
			else
			{
				$holiday_date_start = $validate_holiday['start'];
				$holiday_date_end   = $validate_holiday['end'];
			}
			
			// 他に時間枠と重複があります
			if(\Model_Menusetting::validate_date($holiday_date_start, $holiday_date_end) == 'false')
			{
				return 22;
			}
			
			//validate is_holiday
			if(isset($data['is_holiday']) && $data['is_holiday'])
			{
				foreach ($data['is_holiday'] as $key => $value)
				{
					$rule = '/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/';
					$value = trim($value);
					if($value != null && ! preg_match($rule, $value))
					{
						return 3;
						break;
					}
					
					$date = explode('-', $value);
					if($value != null && checkdate($date[1], $date[2], $date[0]) == false)
					{
						return 3;
						break;
					}
				}
			}
			
			//set data to session
			$session = \Session::instance();
			\Session::set('datamenu', $data);
			
			return 'true';
		}
		
		
		$this->template->content = \View::forge('menu/index');
	}
	
	/*
	 * Save data
	 *
	 * @since 25/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_savedata()
	{
	
		if (\Input::method() == 'POST') 
		{
			if( ! \Session::get('datamenu'))
			{
				return false;
			}
			
			$data = \Session::get('datamenu');
			
			//save data
			$umenu = new \Model_Umenusetting();
			$umenu->umenusetting_save($this->sscode, $data);
			
			//delete session
			\Session::delete('datamenu');
			return 'true';
			die();
		}
		
		return false;
	}
	
	/*
	 * Get menu setting info
	 *
	 * @since 25/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_getinfo()
	{ 
		$menu_code = \Input::post('menu_code');
		
		//get menu_setting
		$menu_info = \Model_Menusetting::get_info($this->sscode, $menu_code);
		if( ! $menu_info)
		{
			return false;
		}
		
		//get open_timer_id
		$open_timer_id0 = \Model_Opentimer::get_open_timer_id($this->sscode, $menu_code, 0);
		$open_timer_id1 = \Model_Opentimer::get_open_timer_id($this->sscode, $menu_code, 1);
		//get open_timer in future
		$menu_info['open_timer0'] = array();
		$menu_info['open_timer1'] = array();
		$list_in_future0 = \Model_Opentimer::get_list_in_future($this->sscode, $menu_code);
		$list_in_future1 = \Model_Opentimer::get_list_in_future($this->sscode, $menu_code, 1);
		$menu_info['open_timer0'] = $this->get_last_opentimer($list_in_future0);
		$menu_info['open_timer1'] = $this->get_last_opentimer($list_in_future1);
		
		//sample open timer detail 
		$open_time_detail0 = array();
		$open_time_detail1 = array();
		//get list timer detail by open_time_id
		if($open_timer_id0)
		{
			$open_time_detail0 = \Model_Opentimerdetail::get_list_timer($open_timer_id0);
		}
		
		if($open_timer_id1)
		{
			$open_time_detail1 = \Model_Opentimerdetail::get_list_timer($open_timer_id1);
		}
		
		//get coating list
		$coating = \Model_Enablecoating::get_list($this->sscode);
		
		//marge arrays
		$menu_info['coating_code'] = \Model_Enablecoating::get_list($this->sscode);
		
		//marge open_time is_holiday 0
		foreach ($open_time_detail0 as $k => $v)
		{
			$menu_info['week-hoursstart'][$k]['start_time_h'] = \Utility::string_to_time($v['start_time']);
			$menu_info['week-minutestart'][$k]['start_time_m'] = \Utility::string_to_time($open_time_detail0[$k]['start_time'], true);
			$menu_info['week-hoursend'][$k]['end_time_h'] = \Utility::string_to_time($v['end_time']);
			$menu_info['week-minutesend'][$k]['end_time_m'] = \Utility::string_to_time($open_time_detail0[$k]['end_time'], true);
		}
				
		//marge open_time is_holiday 1
		foreach ($open_time_detail1 as $k => $v)
		{
			$menu_info['holiday-hoursstart'][$k]['start_time_h'] = \Utility::string_to_time($v['start_time']);
			$menu_info['holiday-minutestart'][$k]['start_time_m'] = \Utility::string_to_time($open_time_detail1[$k]['start_time'], true);
			$menu_info['holiday-hoursend'][$k]['end_time_h'] = \Utility::string_to_time($v['end_time']);
			$menu_info['holiday-minutesend'][$k]['end_time_m'] = \Utility::string_to_time($open_time_detail1[$k]['end_time'], true);
		}
		
		//is holiday
		$menu_info['is_holiday'] = \Model_Stopdate::get_info($this->sscode, $menu_code);
		
		$content_type = array(
			'Content-type' => 'application/json',
			'SUCCESS'      => 0,
		);
		echo new \Response(json_encode($menu_info), 200, $content_type);
		
		return false;
	}
	
	/*
	 * Get one record in last opentimer
	 *
	 * @since 07/07/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function get_last_opentimer($list_opentmer_future)
	{
		$open_timer = array();
		$list_id = array();
		foreach ($list_opentmer_future as $key => $val)
		{
			if($val['start_date'] != null && $val['end_date'] == null)
			{
				if($val['start_date'] < date('Y-m-d'))
				{
					$list_id[] = $val['open_timer_id'];
				}
				
				elseif($val['start_date'] >= date('Y-m-d'))
				{
					$open_timer[$key] = $val;
				}
			}
			
			if($val['start_date'] != null && $val['end_date'] != null)
			{
				if($val['end_date'] >= date('Y-m-d'))
				{
					$open_timer[$key] = $val;
				}
			}
		}
		
		$rs = array();
		
		if(count($list_id))
		{
			$rs = \Model_Opentimer::get_opentimer_last($list_id);
		}
		
		return array_merge($rs,$open_timer);
	}
}
