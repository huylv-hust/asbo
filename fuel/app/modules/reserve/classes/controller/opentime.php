<?php

/**
 * Opentime class
 *
 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
 * @date 26/06/2015
 */

namespace Reserve;

class Controller_Opentime extends \Controller_Usappy
{
	/*
	 * Add or update opentime
	 *
	 * @since 29/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_index()
	{
		$this->template->title = 'Usappyオートサービス管理';

		$open_timer_id = \Input::get('open_timer_id');
		if($open_timer_id)
		{
			$data['opentimer_info'] = \Model_Opentimer::get_opentimer_info($open_timer_id);
			if( ! $data['opentimer_info'])
			{
				\Response::redirect(\Uri::base().'reserve/menu');
			}

			$data['open_timer_detail'] = \Model_Opentimerdetail::get_list_timer($data['opentimer_info']['open_timer_id']);
		}

		if (\Input::method() == 'POST')
		{
			$post = \Input::post();

			//validate start datetime is overlap
			$datetime_overlap = \Model_Opentimer::check_date_overlap($this->sscode, $post);
			if( ! $datetime_overlap)
			{
				return 999;
			}

			// validate start date < end date
			$validate = \Model_Opentimer::validate_startenddate($post);
			if($validate == 'errors')
			{
				return 1;
			}
			else
			{
				$date_start = $validate['start'];
				$date_end   = $validate['end'];
			}

			//validate open_time_detail is overlap
			if(\Model_Opentimer::validate_date($date_start, $date_end) == 'false')
			{
				return 11;
			}

			//set data to session
			$session = \Session::instance();
			\Session::set('dataopentime', $post);

			return 'true';
		}

		$data['open_timer_id'] = $open_timer_id;
		$data['disabled'] = '';
		if(\Input::get('menu_code', '') != '')
		{
			$data['disabled'] = 'disabled';
		}

		$this->template->content = \View::forge('opentime/index', $data);
	}

	/*
	 * Save data
	 *
	 * @since 30/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_savedata()
	{

		if (\Input::method() == 'POST')
		{
			if( ! \Session::get('dataopentime'))
			{
				return false;
			}

			$data = \Session::get('dataopentime');

			//save data
			$uopentimer = new \Model_Uopentimer();
			$uopentimer->uopentimer_save($this->sscode, $data);

			//delete session
			\Session::delete('dataopentime');

			return 'true';
		}

		return false;
	}

	/*
	 * Delete opentimer and open timer detail
	 *
	 * @since 01/07/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function action_delete()
	{
		if (\Input::method() == 'POST')
		{
			$open_timer_id = \Input::post('open_timer_id');
			if($open_timer_id)
			{
				$uopentimer = new \Model_Uopentimer();
				$uopentimer->uopentimer_delete($open_timer_id);

				return 'true';
			}
		}

		return false;
	}
}
