<?php

class Model_Opentimerdetail extends \Orm\Model
{
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

	protected static $_table_name = 'open_timer_detail';
	
	/*
	 * Delete all open time
	 *
	 * @since 22/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function delete_all($open_timer_id)
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
	public static function get_list_timer($open_timer_id)
	{
		return DB::select('*')
				->from(self::$_table_name)
				->where('open_timer_id', $open_timer_id)
				->execute()
				->as_array();
	}

	/*
	 * Save data
	 *
	 * @since 22/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function register($open_timer_id, $data, $flag = 'week')
	{
		if ( ! empty($data[$flag.'-hoursstart']) && ! empty($data[$flag.'-hoursend']))
		{
			foreach ($data[$flag.'-hoursstart'] as $key => $value)
			{
				$insert = array(
					'open_timer_id' => $open_timer_id,
					'start_time'    => Utility::time_to_string($value, $data[$flag.'-minutestart'][$key]), // (hours * 60) + minutes
					'end_time'      => Utility::time_to_string($data[$flag.'-hoursend'][$key], $data[$flag.'-minutesend'][$key]),
					'created_at'    => date('Y-m-d H:i:s'),
					'updated_at'    => date('Y-m-d H:i:s'),
				);
				DB::insert(self::$_table_name)
				->set($insert)
				->execute();
			}
		}
	}
	
	/*
	 * Save data
	 *
	 * @since 30/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function opentimedetail_save($open_timer_id, $data)
	{
		if ( ! empty($data['hoursstart']) && ! empty($data['hoursend']))
		{
			foreach ($data['hoursstart'] as $key => $value)
			{
				$insert = array(
					'open_timer_id' => $open_timer_id,
					'start_time'    => Utility::time_to_string($value, $data['minutestart'][$key]), // (hours * 60) + minutes
					'end_time'      => Utility::time_to_string($data['hoursend'][$key], $data['minutesend'][$key]),
					'created_at'    => date('Y-m-d H:i:s'),
					'updated_at'    => date('Y-m-d H:i:s'),
				);
				DB::insert(self::$_table_name)
				->set($insert)
				->execute();
			}
		}
	}
}
