<?php

class Model_Opentimer extends \Orm\Model
{
	protected static $_primary_key = array('open_timer_id');
	protected static $_properties = array(
		'sscode',
		'menu_code',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events'          => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events'          => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'open_timer';
	
	/*
	 * Delete all open time
	 *
	 * @since 22/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function opentimer_delete($open_timer_id)
	{
		return DB::delete(self::$_table_name)
				->where('open_timer_id', $open_timer_id)
				->execute();
	}
	
	/*
	 * Get open_timer_id
	 *
	 * @since 25/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_open_timer_id($sscode, $menu_code, $is_holiday = 0)
	{
		$query = DB::select('*')
			->from(self::$_table_name)
			->where('sscode', $sscode)
			->and_where('menu_code', $menu_code)
			->and_where('is_holiday', $is_holiday)
			->and_where(DB::expr('start_date IS NULL and end_date IS NULL'));
		
		$result = $query->execute()->as_array();
		
		if($result)
		{
			return $result[0]['open_timer_id'];
		}
		
	}
	
	/*
	 * Get list open timer in future
	 *
	 * @since 29/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_list_in_future($sscode, $menu_code, $is_holiday = 0)
	{
		return DB::select('*')
				->from(self::$_table_name)
				->where('sscode', $sscode)
				->and_where('menu_code', $menu_code)
				->and_where('is_holiday', $is_holiday)
				//->and_where(DB::expr("start_date >= '".date('Y-m-d')."'"))
				->execute()
				->as_array();
	}
	
	/*
	 * Get list open timer in future
	 *
	 * @since 29/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_opentimer_last($list_opentimer_id)
	{
		$list_opentimer_id = implode(',', $list_opentimer_id);
		$result = DB::select('*')
				->from(self::$_table_name)
				->where(DB::expr("open_timer_id in (".$list_opentimer_id.")"))
				->order_by('start_date', 'desc')
				->limit(1)
				->execute()
				->as_array();
		
		return $result;
		
	}
	
	/*
	 * Get open timer info
	 *
	 * @since 26/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_opentimer_info($open_timer_id)
	{
		$result = DB::select('*')
					->from(self::$_table_name)
					->where('open_timer_id', $open_timer_id)
					->execute()
					->as_array();
		if($result)
		{
			return $result[0];
		}
		
	}
	
	/*
	 * Save data with menu_setting
	 *
	 * @since 29/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function opentimer_save($sscode, $data, $is_holiday = 0)
	{
		$menu_code = trim($data['menu_code']);
		$db = array(
			'sscode'     => $sscode,
			'menu_code'  => $menu_code,
			'is_holiday' => $is_holiday,
			'start_date' => null,
			'end_date'   => null,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		);
		
		$result = DB::select('*')
					->from(self::$_table_name)
					->where('sscode', $sscode)
					->and_where('menu_code', $menu_code)
					->and_where('is_holiday', $is_holiday)
					->and_where(DB::expr('start_date IS NULL and end_date IS NULL'))
					->execute()->as_array();
		
		if(count($result)) // update
		{
			$open_timer_id = $result[0]['open_timer_id'];
			unset($db['created_at']);
			DB::update(self::$_table_name)
					->set($db)
					->where('open_timer_id', $open_timer_id)
					->execute();
		}
		else // insert
		{
			$last_id = DB::insert(self::$_table_name)->set($db)->execute();
			$open_timer_id = $last_id[0];
		}
		
		return $open_timer_id;
	}
	
	/*
	 * Save data only open_timer
	 *
	 * @since 30/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function opentimer_savedata($sscode, $data)
	{
		$open_timer_id = $data['open_timer_id'];
		$db = array(
			'sscode'     => $sscode,
			'menu_code'  => $data['menu_code'],
			'is_holiday' => $data['is_holiday'],
			'start_date' => $data['start_date'],
			'end_date'   => $data['end_date'] != null ? $data['end_date'] : null,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		);
		
		if($open_timer_id) // update
		{
			$last_id = $open_timer_id;
			//update
			unset($db['created_at']);
			DB::update(self::$_table_name)
					->set($db)
					->where('open_timer_id', $open_timer_id)
					->execute();
		}
		else // insert
		{
			$get_last_id = DB::insert(self::$_table_name)->set($db)->execute();
			$last_id = $get_last_id[0];
		}
		
		return $last_id;
	}
	
	/*
	 * Check start and end datetime overlap
	 *
	 * @since 30/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function check_date_overlap($sscode, $post)
	{
		$query = DB::select('*')
					->from(self::$_table_name)
					->where('sscode', $sscode)
					->and_where('menu_code', $post['menu_code'])
					->and_where('is_holiday', $post['is_holiday']);
					
		if($post['end_date'])
		{
			$query->and_where('end_date', '>=', $post['start_date']);
			$query->and_where('start_date', '<=', $post['end_date']);
		}
		else
		{
			$query->and_where('start_date', $post['start_date']);
			$query->and_where(DB::expr('end_date IS NULL'));
		}
		
		if($post['open_timer_id'])
		{
			$query->and_where('open_timer_id', '!=', $post['open_timer_id']);
		}
				
		$result = $query->execute()->as_array();
		
		if(count($result))
		{
			return false;
		}
		
		return true;
	}
	
	/*
	 * validate start less than endate
	 *
	 * @since 30/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function validate_startenddate($data)
	{
		if( ! isset($data['hoursstart']))
		{
			return false;
		}
		
		$date_start = array();
		$date_end   = array();
		// sort date time asc
		asort($data['hoursstart']);

		foreach($data['hoursstart'] as $key => $value)
		{
			$day_start = strtotime(date('Y-m-d').' '.$value.':'.$data['minutestart'][$key].':00');
			$day_end = strtotime(date('Y-m-d').' '.$data['hoursend'][$key].':'.$data['minutesend'][$key].':00');
			$date_start[] = $day_start;
			$date_end[]   = $day_end;
			//start date >= enddate
			if($day_end <= $day_start)
			{
				return 'errors';
				break;
			}
		}
		
		return array(
			'start' => $date_start,
			'end'   => $date_end,
		);
	}
	
	/*
	 * validate date
	 *
	 * @since 30/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function validate_date($startdate, $enddate)
	{
		// if time is 
		if(count($startdate) <= 1)
		{
			return false;
		}
		
		foreach ($startdate as $key => $value)
		{
			if($key == (count($startdate) - 1))
			{
				break;
			}

			if($enddate[$key] > ($startdate[$key + 1]))
			{
				return 'false';
				break;					
			}
		}
	}
}
