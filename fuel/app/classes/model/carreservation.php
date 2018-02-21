<?php

/**
 * carreservation class
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 12/05/2015
 */
class Model_Carreservation extends Fuel\Core\Model_Crud
{
	protected static $_primary_key = 'reservation_no';
	protected static $_table_name = 'car_reservation';
	protected static $_rules = array(
		'car_id'     => 'required',
		'start_time' => 'required|valid_date',
		'end_time'   => 'required|valid_date',
		'sscode'     => 'required',
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
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $reservation_no
	 * @return type
	 */
	public function get_car_reservation_info($reservation_no)
	{
		return static::forge()->find_by_pk($reservation_no);
	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $car_id
	 * @param type $start_time
	 * @param type $end_time
	 * @return type
	 */
	public function get_list_reservation($car_id, $start_time = '1970-01-01', $end_time = '2100-01-01')
	{
		$config = array(
			'where' => array(
				array(
					'car_id',
					'=',
					$car_id,
				),
				array(
					'start_time',
					'>=',
					$start_time,
				),
				array(
					'end_time',
					'<=',
					$end_time,
				),
			)
		);
		$rs = static::forge()->find($config);
		return $this->object_to_array($rs);

	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $reservation_no
	 * @return type
	 */
	public function car_reservation_delete($reservation_no)
	{
		$reservation = static::forge()->find_by_pk($reservation_no);
		return $reservation->delete();
	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $data
	 * @param type $reservation_no
	 * @return boolean
	 */
	public function car_reservation_save($data, $reservation_no = '')
	{
		if ( ! count($data))
			return array();
		if ($reservation_no == '')
		{
			$reservation = static::forge();
			$reservation->set($data);
			return $reservation->save();
		}
		else
		{
			$reservation = static::forge()->find_by_pk($reservation_no);
			if (count($reservation))
			{
				$reservation->set($data);
				return $reservation->save();
			}

			return array();
		}
	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $data
	 * @return type
	 */
	public function object_to_array($data)
	{
		$result = array();
		$j = 0;
		foreach ($data as $_temp)
		{
			foreach ($_temp as $k => $v)
				$result[$j][$k] = $v;
			++$j;
		}

		return $result;
	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $id
	 * @param type $sscode
	 * @return type
	 */
	public function get_reservation_info($id)
	{
		$query = DB::select(DB::expr('start_time as start ,end_time as end ,reservation_no,car_reservation.sscode'))
				->from(self::$_table_name)
				->join('car')
				->on('car_reservation.car_id', '=', 'car.car_id');
		if ($id)
		{
			$query->where('car_reservation.car_id', '=', $id);
		}
		else
		{
			$min_id = DB::select(DB::expr('(car_reservation.car_id)'))
					->from(self::$_table_name)->execute()->as_array();
			$query->where('car_reservation.car_id', '=', $min_id[0]['car_id']);
		}

		$result = $query->execute()->as_array();
		$list_ss_info = \api::get_ss_name() ;
		$ss = array();
		foreach ($list_ss_info as $key => $value)
		{
			$ss[$value['sscode']] = $value['ss_name'];
		}

		foreach ($result as $key => $value)
		{
			$ssname = '';
			if(isset($ss[$value['sscode']]))
			{
				$ssname = $ss[$value['sscode']];
			}

			$result[$key]['title'] = $ssname;
		}

		return $result;
	}
	/**
	 *
	 * @return type
	 */
	public function get_min_id()
	{
		$min_id = DB::select(DB::expr('(car_reservation.car_id)'))
					->from(self::$_table_name)->execute()->as_array();
		return  $min_id[0]['car_id'];
	}

}
