<?php

namespace Repair;
use \Model\Repaireventplan;
use \Model\Repairreport;

class Controller_Plan extends \Controller_Usappy
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
		$this->create_cookie_ssinfo('repair');
		$data = $this->get_cookie_ssinfo('repair');

		$model = new \Model_Repaireventplan();
		if(\Input::post('year'))
		{
			$year = \Input::post('year');
		}
		else
		{
			if(\Input::get('year'))
			{
				$year = \Input::get('year');
			}
			else
			{
				$year = date('Y');
			}
		}

		$config = ['pagination_url' => \Uri::base().'repair/plan/index?year='.$year,
			'total_items'    => count($model->get_list(\Cookie::get('sscode'),null,$year)),
			'per_page'       => \Constants::$per_page,
			'uri_segment'    => 4,
			'num_links'      => \Constants::$num_links,
			'link_offset'    => 1
			];
		$pagination = \Pagination::forge('mypagination', $config);
		$data['year'] = $year;
		$data['list'] = $model->get_list(\Cookie::get('sscode'),$pagination,$year);

		if(isset($_POST['event_name']))
		{
			$event_name   = \Input::param('event_name');
			$start_date   = \Input::param('start_date');
			$end_date     = \Input::param('end_date');
			$sscode       = \Cookie::get('sscode');
			$piece_count  = \Input::param('piece_count');
			$target_sales = \Input::param('target_sales');
			$event_id     = \Input::param('event_id');
			$year		  = \Input::param('year');
			$created_at   = date('Y-m-d H:i:s');
			$updated_at   = date('Y-m-d H:i:s');

			$data = compact('event_name', 'start_date', 'end_date','piece_count','sscode','target_sales','updated_at');

			if((int)$event_id != -1)
			{
				$model = $model->find($event_id);
			}
			else
			{
				$data["created_at"] = $created_at ;
			}

			$model->set($data);
			$model->save();
			\Response::redirect(\Uri::current().'?year='.$year);
		}
		$this->template->title = 'Usappyオートサービス管理';
		$this->template->content = \View::forge('plan/index', $data);

	}

	public function action_detail_event()
	{
		$model    = new \Model_Repaireventplan();
		$event_id = \Input::param('id');
		$data     = $model->get_event_info((int) $event_id);
		$content_type = array('Content-type' => 'application/json');
		return new \Response(json_encode($data), 200, $content_type);
	}
	public function action_delete_event()
	{
		$sscode = \Cookie::get('sscode');
		$id     = \Input::param('id');
		$model = new \Model_Repaireventplan();
		$result = \DB::delete('repair_event_plan')->where('event_id', '=', $id)->execute();
		exit();

	}
}
