<?php

/**
 * List class
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 19/05/2015
 */

namespace Reserve;

class Controller_List extends \Controller_Usappy
{
	/*
	 * List all pit
	 *
	 * @since 09/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */

	public function action_index()
	{
		$this->template->title = 'Usappyオートサービス管理';
		$reservation = new \Model_Reservation();
		$pit = new \Model_Pit();
		$cs = new \Model_Customer();
		$data = array();
		$sscode_default = \Cookie::get('sscode');
		$start_time = \Input::param('start_time');
		$end_time = \Input::param('end_time');
		$plate_no = \Input::param('plate_no');
		$usappy_id = \Input::param('usappy_id');
		$reservation_no = \Input::param('reservation_no');
		$config = array();
		$config_pagination = array();
		$rs = true;
		if($reservation_no)
		{
			$config['where'][] = array(
				'reservation_no',
				'=',
				$reservation_no,
			);
			$config_pagination[] = array(
				'reservation_no',
				'=',
				$reservation_no,
			);
		}

		if($plate_no)
		{
			$config['where'][] = array(
				'plate_no',
				'=',
				$plate_no,
			);
			$config_pagination[] = array(
				'plate_no',
				'=',
				$plate_no,
			);
		}

		if($start_time)
		{
			$config['where'][] = array(
				'start_time',
				'>=',
				$start_time,
			);
			$config_pagination[] = array(
				'start_time',
				'>=',
				$start_time,
			);
		}

		if($end_time)
		{
			$config['where'][] = array(
				'end_time',
				'<=',
				$end_time.' 23:59',
			);
			$config_pagination[] = array(
				'end_time',
				'<=',
				$end_time.' 23:59',
			);
		}

		if($usappy_id)
		{
			$member = \Api::get_info_card($usappy_id);
			if(isset($member['member_kaiinCd']))
			{
				$config['where'][] = array(
					'member_id',
					'=',
					$member['member_kaiinCd'],
				);
				$config_pagination[] = array(
					'member_id',
					'=',
					$member['member_kaiinCd'],
				);
			}
			else
			{
				$rs = false;
			}
		}

		$data['is_search'] = (int)\Input::get('search');
		if(\Input::get('search'))
		{
			if(\Input::get('sscode'))
			{
				$config['where'][] = array(
					'sscode',
					'=',
					\Input::param('sscode'),
				);
				$config_pagination[] = array(
					'sscode',
					'=',
					\Input::param('sscode'),
				);
			}
		}
		else
		{
			$config['where'][] = array(
				'sscode',
				'=',
				$sscode_default,
			);
			$config_pagination[] = array(
				'sscode',
				'=',
				$sscode_default,
			);
		}

		$data['start_time'] = $start_time;
		$data['end_time'] = $end_time;
		$data['plate_no'] = $plate_no;
		$data['reservation_no'] = $reservation_no;
		$data['usappy_id'] = $usappy_id;
		$config_pagination = array(
			'pagination_url' => \Uri::base().'reserve/list/index?'.http_build_query(\Input::get()),
			'total_items'    => $rs ? $reservation->count('reservation_no',true,$config_pagination) : 0,
			'per_page'       => \Constants::$per_page,
			'uri_segment'    => 4,
			'num_links'      => \Constants::$num_links,
			'link_offset'    => 1,
		);

		//set cookie return url
		\Cookie::set('reserve_return_url', \Uri::current().'?'.http_build_query(\Input::get()), 60 * 60 * 24);

		$pagination = \Pagination::forge('reservepagination', $config_pagination);
		$config['limit'] = $pagination->per_page;
		$config['offset'] = $pagination->offset;
		$config['order_by'] = array('start_time' => 'desc');
		$list = array();
		if($rs)
		{
			$list = $reservation -> search_reservation_list($config);
		}

		$data['list'] = $list;
		$_sscode = array();
		$_pit_no = array();
		foreach($list as $_temp)
		{
			$_sscode[] = $_temp['sscode'];
			$_pit_no[] = $_temp['pit_no'];
		}

		$list_pit = $pit->search_pit_list($_sscode, $_pit_no);//SELECT p.pit_name,r.sscode,r.pit_no FROM `pit` as p right join `reservation` as r on (p.sscode = r.sscode AND p.pit_no =r.pit_no) LIMIT 0, 25
		$pit_name = array();
		foreach($list as $_temp)
		{
			foreach($list_pit as $_temp_pit)
			{
				if($_temp['sscode'] == $_temp_pit['sscode'] && $_temp['pit_no'] == $_temp_pit['pit_no'])
				{
					$pit_name[$_temp['sscode']][$_temp['pit_no']] = $_temp_pit['pit_name'];
				}
			}
		}

		$list_ss = \Api::get_ss_name();
		$_ss_name_code = array();
		foreach ($list_ss as $_temp)
		{
			$_ss_name_code[$_temp['sscode']] = $_temp['ss_name'];
		}

		$data['list_pit_name'] = $pit_name;
		$data['list_ss'] = $_ss_name_code;
		$list_cs = $cs->get_list_member_info($list);
		$data['list_cs'] = $list_cs;
		\Session::set('url_redirect',\Uri::base().'reserve/list/index/'.(\Uri::segment(4) ? \Uri::segment(4):1).'?'.http_build_query(\Input::get()));
		$this->template->content = \View::forge('list/index', $data);
		$this->template->content->ssfinder = \View::forge('partials/ssfinder');

	}
	/**
	 * delete
	 * @return \Response
	 */
	public function action_delete()
	{

		$reservation_no = \Input::param('reservation_no');
		$reservation = new \Model_Reservation();
		$rs = $reservation->reservation_delete($reservation_no);
		if($rs)
		{
			return new \Response('1', 200,array());
		}
		else
		{
			return new \Response('0', 200,array());
		}
	}

}
