<?php

/**
 * Pit class
 *
 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
 * @date 09/05/2015
 */

namespace Reserve;

class Controller_Pit extends \Controller_Usappy
{
	/*
	 * List all pit
	 *
	 * @since 09/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */

	public function action_index()
	{
		$this->template->title = 'Usappyオートサービス管理 - Reserve Pit';

		//config pagination
		$config = array(
			'pagination_url' => \Uri::base().'reserve/pit/index',
			'total_items'    => count(\Model_Pit::get_pit_list($this->sscode)),
			'per_page'       => \Constants::$per_page,
			'uri_segment'    => 4,
			'num_links'      => \Constants::$num_links,
			'link_offset'    => 1
		);
		
		$pagination = \Pagination::forge('pitpagination', $config);
		
		//get all pit by sscode
		$data['listpit'] = \Model_Pit::get_pit_list($this->sscode, $pagination->per_page, $pagination->offset);

		//get all pit menu by sscode
		$list_menu_pit = \Model_Pitenablemenu::get_pit_menu_list($this->sscode);
		
		//marge two array
		$data['listmenupit'] = array();
		foreach ($list_menu_pit as $items) 
		{
			$data['listmenupit'][$items['pit_no']][] = $items;
		}

		$this->template->content = \View::forge('pit/index', $data);
	}

	/*
	 * Insert and edit pit
	 *
	 * @since 11/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */

	public function action_input($pit_no = null)
	{
		if (\Input::method() == 'POST') 
		{
			$data = \Input::post();
			$upit = new \Model_Upit();
			$upit->upit_save($this->sscode, $data);	
		}
		
		return false;
	}
	
	/*
	 * Get pin info
	 *
	 * @since 15/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_getpitinfo()
	{
		$pit_no = \Input::param('pit_no');
		//Get pit info by pit_no
		$pitinfo = \Model_Pit::get_pit_info($this->sscode, $pit_no);
		
		//Add pitmenu to array
		if($pitinfo)
		{
			$pitinfo[0]['pitmenu'] = \Model_Pitenablemenu::get_pit_menu_list($this->sscode, $pit_no);
			
			$content_type = array('Content-type'=>'application/json','SUCCESS' => 0);
			echo new \Response(json_encode($pitinfo[0]), 200, $content_type);
		}
		
		return false;
	}
	
	/*
	 * Delete pit
	 *
	 * @since 15/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_delete()
	{
		$pit_no = \Input::param('pit_no');
		$upit = new \Model_Upit();
		
		$status = $upit->upit_delete($this->sscode, $pit_no);
		if($status == 'false'){
			echo 'false';
			die();
		}

		return false;
	}
	
	/*
	 * Check unique pit_name
	 *
	 * @since 22/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_unique()
	{
		if (\Input::method() == 'POST') 
		{
			$pit_no = \Input::post('pit_no');
			$pit_name = \Input::post('pit_name');
			$query = \Model_Pit::query()
					->where('sscode', $this->sscode)
					->where('pit_name', $pit_name);
			
			if($pit_no != 0)
			{
				$query->where('pit_no', '!=', $pit_no);
			}
			
			if($query->count())
			{
				return 'unique';
			}
		}
		
		return false;
	}
}
