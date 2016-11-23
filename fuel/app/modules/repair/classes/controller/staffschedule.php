<?php

namespace Repair;
use \Model\Repairestaff;
use \Model\Repairschedule;

class Controller_Staffschedule extends \Controller_Usappy
{

	/**
	 * Staffschedule
	 * @author NamNT
	 * @since 1.0.0
	 * @param 
	 * @return index
	*/
	public function action_index()
	{
		$repairestaff = new \Model_Repairestaff;
		$data['staff_id'] = \Cookie::get('staff_id');
		$staff_name = $repairestaff->get_staffs_info(\Cookie::get('staff_id'));
		$data['staff_name'] = $staff_name['staff_name'];
		$this->template->title = 'Usappyオートサービス管理';
		$this->template->content = \View::forge('staffschedule/index', $data);
		$this->template->content->stafffinder = \View::forge('partials/stafffinder');

	}
	
	public function action_search_staff()
	{
		$repairestaff = new \Model_Repairestaff;	
		$branch = \Input::param('branch');
		$key = \Input::param('name');
		$data = $repairestaff->get_repair_staff_list($branch, $key);
		$content_type = array('Content-type' => 'application/json');
		return new \Response(json_encode($data), 200, $content_type);
	}
	
	public function action_set_cookie_staff()
	{
		$staff_id = \Input::param('repair_staff_id');
		$staff_name = \Input::param('staff_name');
		\Cookie::set('staff_id',$staff_id, 60 * 60 * 24);
		\Cookie::set('staff_name',$staff_name, 60 * 60 * 24);
		exit();
	}
	/**
	 * Get repair schedule
	 * @author NamNT
	 * @since 1.0.0
	 * @param $sscode
	 * @return $datas
	*/
	public function action_get_booking_data()
	{
		$repair_schedule = new \Model_Repairschedule;
		$staff_id = \Cookie::get('staff_id');
		
		$datas = $repair_schedule->get_repair_schedule_list($staff_id);
		$content_type = array('Content-type' => 'application/json');
		return new \Response(json_encode($datas), 200, $content_type);
	}
}
