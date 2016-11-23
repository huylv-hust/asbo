<?php

class Model_Car extends \Orm\Model
{

	protected static $_primary_key = array('car_id');
	protected static $_table_name = 'car';

	/**
	 * Get List Cars
	 * @author NamNT
	 * @since 1.0.0
	 * @param $sscode
	 * @return list car
	*/
	public function get_car_list($sscode)
	{
		$result = DB::select()->from('car')->where('sscode', $sscode)->execute()->as_array();
		return $result;
	}
	/**
	 * Get Car Info
	 * @author NamNT
	 * @since 1.0.0
	 * @param $car_id
	 * @return Info of car
	*/
	public function get_car_info($car_id)
	{
		$query = DB::select('*')
			->from(self::$_table_name)
			->where('car_id', '=', $car_id);
		$result = $query->execute()->as_array();

		return $result[0];
	}

}
