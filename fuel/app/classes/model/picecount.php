<?php

class Model_Picecount extends \Orm\Model
{

	protected static $_primary_key = array('repair_staff_id');
	protected static $_properties = array(
		'repair_staff_id',
		'year',
		'month',
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
	
	protected static $_table_name = 'pice_count';

	/*
	 * List pice count
	 *
	 * @since 29/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_picecount_list($repair_staff_id)
	{
		$now_date = date('Y-m');
		$query = DB::query("SELECT * FROM `pice_count` WHERE CONCAT(`pice_count`.year,'-',`pice_count`.month) > '{$now_date}' AND repair_staff_id = '{$repair_staff_id}'");	
		return $query->execute()->as_array();
	}

	/*
	 * Save data
	 *
	 * @since 29/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function register($data, $repair_staff_id)
	{
		if ( ! isset($data['pice_year']))
		{
			return false;
		}
		
		//unique month year and past
		$month_year = array();
		foreach ($data['pice_year'] as $k => $v)
		{
			$monthyear = $v.'-'.$data['pice_month'][$k].'-01';
			$nowmonth  = date('Y-m').'-01';
			if(strtotime($monthyear) >= strtotime($nowmonth))
			{
				$month_year[$k] = abs($v).'-'.abs($data['pice_month'][$k]);
			}
		}
		
		$new_month_year = array_unique($month_year);
		
		//insert to database
		foreach ($new_month_year as $key => $value)
		{
			$insert = array(
				'repair_staff_id' => $repair_staff_id,
				'year'            => $data['pice_year'][$key],
				'month'           => $data['pice_month'][$key],
				'piece_count'     => $data['pice_counts'][$key],
				'created_at'      => date('Y-m-d H:i:s'),
				'updated_at'      => date('Y-m-d H:i:s'),
			);
			DB::insert(self::$_table_name)
			->set($insert)
			->execute();
		}

	}

	/*
	 * Delete data
	 *
	 * @since 29/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function picecount_delete($repair_staff_id)
	{
		if($repair_staff_id == null)
		{
			return false;
		}
		
		return DB::delete(self::$_table_name)
				->where('repair_staff_id', $repair_staff_id)
				->execute(); 
	}

}
