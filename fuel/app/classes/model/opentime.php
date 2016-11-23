<?php

class Model_Opentime extends \Orm\Model
{
	protected static $_primary_key = array(
		'sscode', 
		'menu_code',
		'is_holiday',
		'start_time',
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

	protected static $_table_name = 'open_time';
	
	/*
	 * Delete all open time
	 *
	 * @since 22/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function delete_all($sscode, $menu_code)
	{
		return DB::delete(self::$_table_name)
				->where('sscode', $sscode)
				->and_where('menu_code', $menu_code)
				->execute();
	}
	
	/*
	 * Get data
	 *
	 * @since 22/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_list($sscode, $menu_code, $is_holiday = 0)
	{
		return DB::select('*')
				->from(self::$_table_name)
				->where('sscode', $sscode)
				->and_where('menu_code', $menu_code)
				->and_where('is_holiday', $is_holiday)
				->execute()
				->as_array();
		
	}

	/*
	 * Save data
	 *
	 * @since 22/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function register($data, $sscode)
	{
		//save is_holiday 0
		if ( ! empty($data['week-hoursstart']) && ! empty($data['week-hoursend']))
		{
			foreach ($data['week-hoursstart'] as $key => $value)
			{
				$insert = array(
					'sscode'     => $sscode,
					'menu_code'  => $data['menu_code'],
					'is_holiday' => 0,
					'start_time' => Utility::time_to_string($value, $data['week-minutestart'][$key]), // (hours * 60) + minutes
					'end_time'   => Utility::time_to_string($data['week-hoursend'][$key], $data['week-minutesend'][$key]),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				);
				DB::insert(self::$_table_name)
				->set($insert)
				->execute();
			}
		}
		
		//save is_holiday 1
		if ( ! empty($data['holiday-hoursstart']) && ! empty($data['holiday-hoursend']))
		{
			foreach ($data['holiday-hoursstart'] as $key => $value)
			{
				$insert = array(
					'sscode'     => $sscode,
					'menu_code'  => $data['menu_code'],
					'is_holiday' => 1,
					'start_time' => Utility::time_to_string($value, $data['holiday-minutestart'][$key]),
					'end_time'   => Utility::time_to_string($data['holiday-hoursend'][$key], $data['holiday-minutesend'][$key]),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				);
				DB::insert(self::$_table_name)
				->set($insert)
				->execute();
			}
		}
	}
}
