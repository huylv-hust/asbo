<?php

/**
 * Staffs class
 *
 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
 * @date 29/05/2015
 */

namespace Repair;

class Controller_Staff extends \Controller_Usappy
{
	/*
	 * Add and edit staff
	 *
	 * @since 29/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_index()
	{
		$this->template->title = 'Usappyオートサービス管理 - Repair Staffs';
		
		$repair_staff_id = \Input::param('repair_staff_id');		
		
		$data['info'] = array();
		if($repair_staff_id)
		{
			$data['info'] = \Model_Repairestaff::get_staffs_info($repair_staff_id);
			$data['info']['pice'] = \Model_Picecount::get_picecount_list($repair_staff_id);
		}
		
		//record not found
		if($repair_staff_id && ! $data['info'])
		{
			\Response::redirect(\Uri::base().'welcome/404');
		}
		
		//If is submit
		if (\Input::method() == 'POST') 
		{
			$post = \Input::post();
			$data['errors'] = null;
			$model = new \Model_Urepairstaffs();
			
			$model->urepairstaffs_save($post, $repair_staff_id);
			
			//if edit from search list
			if(\Cookie::get('return_url_search'))
			{
				$return_url = \Cookie::get('return_url_search');
				\Response::redirect($return_url);
			}
			else 
			{
				\Response::redirect(\Uri::base().'repair/staffs');
			}
		}
		
		$this->template->content = \View::forge('staff/index', $data);
		$this->template->content->ssfinder = \View::forge('partials/ssfinder');
	}
	
	/*
	 * Validate staff input
	 *
	 * @since 04/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_validate()
	{
		
		if (\Input::method() == 'POST') 
		{
			$data = \Input::post();
			$repair_staff_id = $data['repair_staff_id'];
			if( ! isset($data['pice_year']))
			{
				$data['pice_year'] = array();
			}
			
			if( ! isset($data['pice_month']))
			{
				$data['pice_month'] = array();
			}
			
			if( ! isset($data['pice_counts']))
			{
				$data['pice_counts'] = array();
			}
			
			//validate piece year
			if(\Model_Repairestaff::validate_piece_year($data['pice_year']) == 'false')
			{
				return 1;
			}
			
			//validate piece month
			if(\Model_Repairestaff::validate_piece_month($data['pice_month']) == 'false')
			{
				return 2;
			}
			
			//validate has been past
			$validate_past_pice = \Model_Repairestaff::validate_piece($data['pice_year'], $data['pice_month'], $data['pice_counts']);
			if($validate_past_pice === 'piece')
			{
				return 0;
			}
			
			if($validate_past_pice === 'false')
			{
				return 6;
			}
			
			//validate piece count
			if(\Model_Repairestaff::validate_piece_count($data['pice_counts']) == 'false')
			{
				return 3;
			}
			
			//check piece count
			$piece = \Model_Repairestaff::validate_piece_count($data['pice_counts']);
			if($piece == 'rezo' || $piece == 'isnum')
			{
				return 0;
			}
			
			if($piece == 'length')
			{
				return 5;
			}
			
			//Check unique login_id
			$unique = \Model_Repairestaff::get_staffs_unique($data['login_id'], $repair_staff_id);
			if($unique >= 1)
			{
				return 4;
			}
			
			return 'true';			
		}
		
		return false;
	}
}