<?php

class Model_Menusetting extends \Orm\Model
{
	protected static $_primary_key = array(
		'sscode', 
		'menu_code',
	);
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

	protected static $_table_name = 'menu_setting';

	/*
	 * Get list menu by sscode
	 *
	 * @since 21/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_list($sscode)
	{
		return DB::select('*')
				->from(self::$_table_name)
				->where('sscode', $sscode)				
				->execute()
				->as_array();
	}
	
	/*
	 * Get list menu by sscode
	 *
	 * @since 21/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_info($sscode, $menu_code)
	{
		$result = DB::select('*')
				->from(self::$_table_name)
				->where('sscode', $sscode)
				->and_where('menu_code', $menu_code)
				->execute()
				->as_array();
		if($result)
		{
			return $result[0];
		}
	}


	/*
	 * Save data
	 *
	 * @since 21/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function menusetting_save($sscode, $data)
	{
		$menu_code = trim($data['menu_code']);
		$max_parallel_count = empty($data['max_parallel_count']) ? null : $data['max_parallel_count'];
		$db = array(
			'sscode'             => $sscode,
			'menu_code'          => $data['menu_code'],
			'max_parallel_count' => $max_parallel_count,
			'created_at'         => date('Y-m-d H:i:s'),
			'updated_at'         => date('Y-m-d H:i:s'),
		);
		
		$query = self::query()
					->where('sscode', $sscode)
					->where('menu_code', $menu_code);
		if($query->count()) // update
		{
			unset($db['created_at']);
			DB::update(self::$_table_name)
					->set($db)
					->where('sscode', '=', $sscode)
					->and_where('menu_code', '=', $data['menu_code'])
					->execute();
		}
		else // insert
		{
			DB::insert(self::$_table_name)->set($db)->execute();
		}
		
	}
	
	/*
	 * validate start less than endate
	 *
	 * @since 03/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function validate_startenddate($data, $object)
	{
		if( ! isset($data[$object.'-hoursstart']))
		{
			return false;
		}
		
		$dayinweek_date_start = array();
		$dayinweek_date_end   = array();

		foreach($data[$object.'-hoursstart'] as $key => $value)
		{
			$dayinweek_start = strtotime(date('Y-m-d').' '.$value.':'.$data[$object.'-minutestart'][$key].':00');
			$dayinweek_end = strtotime(date('Y-m-d').' '.$data[$object.'-hoursend'][$key].':'.$data[$object.'-minutesend'][$key].':00');
			$dayinweek_date_start[] = $dayinweek_start;
			$dayinweek_date_end[]   = $dayinweek_end;
		}
		
		asort($dayinweek_date_start);
		
		foreach ($dayinweek_date_start as $k => $v)
		{
			//start date >= enddate
			if($dayinweek_date_end[$k] <= $v)
			{
				return 'errors';
				break;
			}
		}
		
		return array(
			'start' => $dayinweek_date_start,
			'end'   => $dayinweek_date_end,
		);
	}
	
	/*
	 * validate date
	 *
	 * @since 21/05/2015
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
