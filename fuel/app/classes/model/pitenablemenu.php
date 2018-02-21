<?php

class Model_Pitenablemenu extends \Orm\Model
{

	protected static $_primary_key = array('sscode', 'pit_no', 'menu_code');
	protected static $_properties = array(
		'sscode',
		'pit_no',
		'menu_code',
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
	protected static $_table_name = 'pit_enable_menu';

	/*
	 * Get list pit menu by sscode
	 *
	 * @since 09/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_pit_menu_list($sscode, $pit_no = null)
	{
		$result = DB::select('*')
				->from(self::$_table_name)
				->where('sscode', $sscode);
		if($pit_no)
		{
			$result->and_where('pit_no', $pit_no);
		}
			
		return $result->execute()->as_array();
	}

	/*
	 * Delete all pitmenu
	 *
	 * @since 11/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */

	public static function delete_all($sscode, $pit_no)
	{
		return DB::delete(self::$_table_name)
				->where('sscode', $sscode)
				->and_where('pit_no', $pit_no)
				->execute();
	}

	/*
	 * Save data
	 *
	 * @since 11/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */

	public static function register($data, $sscode, $pit_no)
	{
		if ( ! $data)
		{
			return false;
		}
		
		foreach ($data as $key => $value)
		{
			$insert = array(
				'sscode'     => $sscode,
				'pit_no'     => $pit_no,
				'menu_code'  => $value,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			);
			DB::insert(self::$_table_name)
			->set($insert)
			->execute();
		}

	}

}
