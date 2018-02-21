<?php

class Model_Stopdate extends \Orm\Model
{
	protected static $_primary_key = array(
		'sscode', 
		'menu_code',
		'stop_date',
	);
	protected static $_properties = array(		
		'sscode',
		'menu_code',
		'stop_date',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'stop_date';
	
	/*
	 * Get data
	 *
	 * @since 22/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_info($sscode, $menu_code)
	{
		return DB::select('*')
				->from(self::$_table_name)
				->where('sscode', $sscode)
				->and_where('menu_code', $menu_code)
				->and_where('stop_date', '>=', date('Y-m-d'))
				->execute()
				->as_array();
	}
	
	/*
	 * Delete all stop date
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
	 * Save data
	 *
	 * @since 22/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function register($data, $sscode)
	{
		
		if (empty($data['is_holiday']))
		{
			return false;
		}
		
		//remove dulicate stop date
		$is_holiday = array_unique($data['is_holiday']);
		
		foreach ($is_holiday as $key => $value)
		{
			if($value >= date('Y-m-d'))
			{
				$insert = array(
					'sscode'     => $sscode,
					'menu_code'  => $data['menu_code'],
					'stop_date' => $value,
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
