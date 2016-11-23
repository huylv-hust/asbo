<?php

/**
 * List class
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 19/05/2015
 */
namespace Repair;

class Controller_List extends \Controller_Usappy
{
	/*
	 * List all pit
	 * @since 09/05/2015
	 * @author NamDD <NamDD@seta-asia.com.vn>
	 */

	public function action_index()
	{
		$this->template->title = 'Usappyオートサービス管理';
		$reservation = new \Model_Repairreservation();
		$staff = new \Model_Repairestaff();
		$cs = new \Model_Customer();
		$data = array();
		$sscode_default = \Cookie::get('sscode');
		$start_time = \Input::param('start_time');
		$end_time = \Input::param('end_time');
		$plate_no = \Input::param('plate_no');
		$reservation_no = \Input::param('reservation_no');
		$card_no = \Input::param('card_no');
		$branch_code = \Input::param('branch','0');
		$config = array();
		$config_pagination = array();
		$rs = true;
		$branch = \Constants::$branch;
		$branch['0'] = '全て';
		ksort($branch);
		$data['branch'] = \Constants::array_to_select( $branch,'','','branch',$branch_code,array('class' => 'form-control'));
		$config['where'] = array();
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

		if($start_time)
		{
			$config['where'][] = array(
				'arrival_time',
				'>=',
				$start_time.' 00:00',
			);
			$config_pagination[] = array(
				'arrival_time',
				'>=',
				$start_time.' 00:00',
			);
		}

		if($end_time)
		{
			$config['where'][] = array(
				'arrival_time',
				'<=',
				$end_time.' 23:59',
			);
			$config_pagination[] = array(
				'arrival_time',
				'<=',
				$end_time.' 23:59',
			);
		}

		if($card_no)
		{
			$member = \Api::get_info_card($card_no);
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

		$repair_staff_ids = array();

		if($branch_code)
		{
			$array_sscode = array();
			$list_ss = \Api::search($branch_code);
			foreach($list_ss as $_temp)
			{
				$array_sscode[] = $_temp['sscode'];
			}

			if(count($array_sscode))
			{
				$config['where'][] = array(
					'sscode',
					'IN',
					$array_sscode,
				);
				$config_pagination[] = array(
					'sscode',
					'IN',
					$array_sscode,
				);
			}
			else
			{
				$rs = false;
			}
		}

		$data['start_time'] = $start_time;
		$data['end_time'] = $end_time;
		$data['plate_no'] = $plate_no;
		$data['reservation_no'] = $reservation_no;
		$data['card_no'] = $card_no;
		$config_pagination = array(
			'pagination_url' => \Uri::base().'repair/list/index?'.http_build_query(\Input::get()),
			'total_items'    => $rs ? $reservation->count('reservation_no',true,$config_pagination) : 0,
			'per_page'       => \Constants::$per_page,
			'uri_segment'    => 4,
			'num_links'      => \Constants::$num_links,
			'link_offset'    => 1,
		);

		//set cookie return url
		\Cookie::set('repair_retun_url', \Uri::current().'?'.http_build_query(\Input::get()), 60 * 60 * 24);

		$pagination = \Pagination::forge('reservepagination', $config_pagination);
		$config['limit'] = $pagination->per_page;
		$config['offset'] = $pagination->offset;
		$config['order_by'] = array('arrival_time' => 'desc');
		$list = array();
		if($rs)
		{
			$list = $reservation ->search_repair_reservation_list($config);
		}

		$data['list'] = $list;
		$list_ss = \Api::get_ss_name();
		$_ss_name_code = array();
		foreach ($list_ss as $_temp)
		{
			$_ss_name_code[$_temp['sscode']] = $_temp['ss_name'];
		}

		$data['total_a_piece_count'] = $rs ? $reservation->sum('a_piece_count',$config['where']) : 0;
		$data['total_b_piece_count'] = $rs ? $reservation->sum('b_piece_count',$config['where']) : 0;
		$data['list_ss'] = $_ss_name_code;
		$list_cs = $cs->get_list_member_info($list);
		$data['list_cs'] = $list_cs;
		\Session::set('url_redirect_repair',\Uri::base().'repair/list/index/'.( \Uri::segment(4) ? \Uri::segment(4) : 1 ).'?'.http_build_query(\Input::get()));
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
		$reservation = new \Model_Repairreservation();
		$rs = $reservation->repair_reservation_delete($reservation_no);
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
