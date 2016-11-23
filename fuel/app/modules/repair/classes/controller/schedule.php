<?php

namespace Repair;
use \Model\Repairschedule;
use \Model\Repairestaff;

class Controller_Schedule extends \Controller_Usappy
{

	/**
	 * Index
	 * @author NamNT
	 * @since 1.0.0
	 * @param 
	 * @return index
	*/
	public function action_index()
	{
		$repairestaff = new \Model_Repairestaff();
		$repairschedule = new \Model_Repairschedule();
		
		if(\Cookie::get('staff_id'))
		{
			$staff_info = $repairestaff->get_staffs_info(\Cookie::get('staff_id'));
		}
		
		$data = array();
		if(\Input::param('repair_schedule_id'))
		{
			$data_info = $repairschedule->get_repair_schedule_detail(\Input::param('repair_schedule_id'));
			$data     = $data_info[0];
		}
		else
		{
			$data = array(
				'start_time' => \Input::param('start'),
				'end_time'   => \Input::param('end'),
				'name'       => $staff_info ? $staff_info['staff_name'] : '徳川家康',
				'staff_id'   => $staff_info ? $staff_info['repair_staff_id'] : '',
			);
		}
		if($data['end_time'] != '')
		{	
		
			if(strlen($data['start_time'])>15)
			{
				if(!$this->validateDate($data['start_time']) || !$this->validateDate($data['end_time']))
				{
					$data['err_message'] = '日付が正しくありません。';
				}
			}
			else {
				if(!$this->validateDate($data['start_time'],'Y-m-d') || !$this->validateDate($data['end_time'],'Y-m-d'))
				{
					$data['err_message'] = '日付が正しくありません。';
				}
			}
		}
		else {
			if(!$this->validateDate($data['start_time']))
			{
				$data['err_message'] = '日付が正しくありません。';
			}
		}
		
		
		$this->template->title = 'Usappyオートサービス管理';
		$this->template->content = \View::forge('schedule/index',$data);
		$this->template->content->ssfinder = \View::forge('partials/ssfinder');
		$this->template->content->stafffinder = \View::forge('partials/stafffinder');

	}
	/**
	 * Save Data
	 * @author NamNT
	 * @since 1.0.0
	 * @param 
	 * @return index
	*/
	public function action_save_data()
	{
		$repair_staff_id   = \Input::param('staff_id');
		$sscode   = \Input::param('sscode');
		$from_date_hh = \Input::param('from_date_hh_re');
		$from_date_mm = \Input::param('from_date_mm_re');
		$to_date_hh = \Input::param('to_date_hh_re');
		$to_date_mm = \Input::param('to_date_mm_re');

		$start_time = \Input::param('from_date_re').' '.$from_date_hh.':'.$from_date_mm;
		$end_time = \Input::param('to_date_re').' '.$to_date_hh.':'.$to_date_mm;
		$created_at = date('Y-m-d H:i:s');
		$updated_at = date('Y-m-d H:i:s');

		$data = compact('repair_staff_id', 'sscode', 'start_time','end_time','updated_at');
		$model = new \Model_Repairschedule();
		if(\Input::param('repair_schedule_id'))
		{
		    $model = $model->find(\Input::param('repair_schedule_id'));
		}
		else
		{
		    $data['created_at'] = $created_at ;
		}

		$model->set($data);
		$model->save();
		\Response::redirect(\Uri::base().'repair/staffschedule');
	}
	/**
	 * Delete repair schedule
	 * @author NamNT
	 * @since 1.0.0
	 * @param 
	 * @return index
	*/
	public function action_delete()
	{
		$model = new \Model_Repairschedule();
		$id = \Input::param('repair_schedule_id');
		$datas = $model->repair_schedule_delete($id);
		$content_type = array('Content-type' => 'application/json');
		return new \Response(json_encode($datas), 200, $content_type);
	}
	public function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$d = \DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
}
