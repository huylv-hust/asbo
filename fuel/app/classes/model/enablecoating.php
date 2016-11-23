<?php

class Model_Enablecoating extends \Orm\Model
{
	protected static $_primary_key = array(
		'sscode', 
		'coating_code',
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

	protected static $_table_name = 'enable_coating';
	
	/*
	 * Get data
	 *
	 * @since 22/05/2015
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
	 * Delete all enable_coating
	 *
	 * @since 22/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function delete_all($sscode)
	{
		return DB::delete(self::$_table_name)
				->where('sscode', $sscode)
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
		
		if ( ! isset($data['coating_code']) || empty($data['coating_code']))
		{
			return false;
		}
		
		foreach ($data['coating_code'] as $key => $value)
		{
			$insert = array(
				'sscode'       => $sscode,
				'coating_code' => $value,
				'created_at'   => date('Y-m-d H:i:s'),
				'updated_at'   => date('Y-m-d H:i:s'),
			);
			DB::insert(self::$_table_name)
			->set($insert)
			->execute();
		}
	}

}
