<?php

/**
 * Staffs class
 *
 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
 * @date 27/05/2015
 */

namespace Repair;

class Controller_Staffs extends \Controller_Usappy
{
	/*
	 * List all staffs
	 *
	 * @since 27/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_index()
	{
		$this->template->title = 'Usappyオートサービス管理 - Repair Staffs';
		
		//get search value
		$seacrch_arr = array(
			'staff_name'  => \Input::param('staff_name'),
			'branch_code' => \Input::param('branch_code'),
		);
		
		//set return url after edit
		$pagination_url = \Uri::base().'repair/staffs/index';
		$return_url = \Uri::current();
		if($seacrch_arr['staff_name'] != null || $seacrch_arr['branch_code'] != null)
		{
			$pagination_url = \Uri::base().'repair/staffs/index'.'?'.http_build_query($_GET);
			$return_url = \Uri::current().'?'.http_build_query($_GET);
		}
		
		//setcookie
		\Cookie::set('return_url_search', $return_url, 60 * 60 * 24);
		
		
		//config pagination
		$config = array(
			'pagination_url' => $pagination_url,
			'total_items'    => count(\Model_Repairestaff::get_staff_list(null, null, $seacrch_arr)),
			'per_page'       => \Constants::$per_page,
			'uri_segment'    => 4,
			'num_links'      => \Constants::$num_links,
			'link_offset'    => 1,
		);
		
		//setup pagination
		$pagination = \Pagination::forge('staffs-pagination', $config);
		
		//get all staffs
		$data['liststaffs'] = \Model_Repairestaff::get_staff_list($pagination->per_page, $pagination->offset, $seacrch_arr);
		
		$this->template->content = \View::forge('staffs/index', $data);
	}
	
	/*
	 * Delete staffs
	 *
	 * @since 29/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_active()
	{
		$repair_staff_id = (int)\Input::param('repair_staff_id');
		$status = (int)\Input::param('status');
		if($status != 0 && $status != 1)
		{
			return false;
		}
		
		\Model_Repairestaff::active($status, $repair_staff_id);
		
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


	/*
	 * Delete staffs
	 *
	 * @since 29/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_delete()
	{
		$repair_staff_id = \Input::param('repair_staff_id');
		$umodel = new \Model_Urepairstaffs();
		
		$status = $umodel->urepairstaffs_delete($repair_staff_id);
		if($status == 'false')
		{
			return 'false';
		}
		
		\Response::redirect(\Uri::base().'repair/staffs');
		
	}
}
