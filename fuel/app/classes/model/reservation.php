<?php
/**
 * Reservation class
 *
 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
 * @date 18/05/2015
 */

class Model_Reservation extends Fuel\Core\Model_Crud
{
	protected static $_primary_key = 'reservation_no';
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

	protected static $_table_name = 'reservation';
	public function set_default_data()
	{
		$data = array();
		$fields = \DB::list_columns(self::$_table_name);
		foreach($fields as $k => $v)
		{
			$data[$k] = $v['default'];
		}

		return $data;
	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $data
	 * @param type $reservation_no
	 * @return id insert
	 */
	public function reservation_save($data, $reservation_no = '')
	{
		$data['updated_at'] = date('Y-m-d H:i');
		if ( ! count($data))
			return array();

		if ($reservation_no == '')
		{
			$data['save_from'] = 'ss';
			$data['created_at'] = date('Y-m-d H:i');
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
	 * @param type $reservation_no
	 * @return array
	 */
	public function get_reservation_info($reservation_no)
	{
		$query = DB::select('*')
			->from(self::$_table_name)
			->where('reservation_no', '=', $reservation_no);
		$result = $query->execute()->as_array();
		if(count($result))
		{
			return $result[0];
		}

		return array();
	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $config is format config['where][] = array('name_filed','oper','values')
	 * @return  array
	 */
	public function  search_reservation_list($config)
	{
		$rs = static::forge()->find($config);
		if(count($rs))
		{
			return $this->object_to_array($rs);
		}

		return array();

	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $reservation_no
	 * @return boolean
	 */
	public function reservation_delete($reservation_no)
	{
		$rs = static::forge()->find_by_pk($reservation_no);

		if(count($rs))
		{
			$hashkey = $rs['hashkey'];
			DB::start_transaction();
			if($rs->delete())
			{
				if($hashkey) // Not member
				{
					$cus_oracle = new Model_Customeroracle();
					$info_customer = $cus_oracle->get_member_info_oracle($reservation_no);

					if(count($info_customer))
					{
						$res_delete_oracle = $cus_oracle->customer_delete($reservation_no);
						if( ! $res_delete_oracle)
						{
							DB::rollback_transaction();
							return false;
						}
					}

				}

				DB::commit_transaction();
				return true;
			}

			return false;
		}

		return false;
	}
	/**
	 *
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
	 *
	 * @param type $sscode
	 * @return type
	 */
	public function get_reservation_list($sscode)
	{
		$query = DB::select(DB::expr('start_time as start ,end_time as end ,reservation_no,menu_code as color, menu_name as title'))
			->from(self::$_table_name);
		$query->and_where('reservation.sscode', $sscode);
		$result = $query->execute()->as_array();
		$colors = \constants::$colors ;
		$pit_work = \constants::$pit_work ;

		foreach ($result as $key => $value)
		{
			$result[$key]['color'] = $colors[$value['color']];
			if($value['color'] != 'other')
			{
				$result[$key]['title'] = $pit_work[$value['color']];
			}
		}

		return $result;
	}

	/**
	 * Get Repair Reservation List Data By Day
	 * @author NamNT
	 * @since 1.0.0
	 * @param $sscode
	 * @return array $datas
	*/
	public function get_reservation_list_by_day($sscode)
	{
		$query = DB::select(DB::expr('start_time as start ,end_time as end ,reservation_no,menu_code as color, menu_name as title,arrival_time'))
			->from(self::$_table_name);
		$query->and_where('reservation.sscode', $sscode);
		$query->where('start_time', 'LIKE','%'.date('Y-m-d').'%');
		$result = $query->execute()->as_array();
		$colors = \constants::$colors ;
		$pit_work = \constants::$pit_work ;

		foreach ($result as $key => $value)
		{
			$result[$key]['color'] = $colors[$value['color']];
			if($value['color'] != 'other')
			{
				$result[$key]['title'] = $pit_work[$value['color']];
				$result[$key]['type'] = 'reserve';
			}
		}

		return $result;
	}
	public function get_reservation_list_by_arrival_time($sscode)
	{
		$query = DB::select(DB::expr('arrival_time as start ,reservation_no, menu_name as title,DATE_SUB(DATE_ADD(arrival_time, INTERVAL 1 HOUR),INTERVAL EXTRACT(MINUTE FROM arrival_time) MINUTE) as end,menu_code as color'))
			->from(self::$_table_name);
		$query->where('arrival_time', null, \DB::expr('IS NOT NULL'));
		$query->and_where('reservation.sscode', $sscode);
		$result = $query->execute()->as_array();
		$colors = \constants::$colors ;
		$pit_work = \constants::$pit_work ;

		foreach ($result as $key => $value)
		{
			if($value['color'] != 'other')
			{
				$result[$key]['title'] = $pit_work[$value['color']].'(入庫)';
			}
			else
			{
				$result[$key]['title'] = $value['title'].'(入庫)';
			}

			$result[$key]['color'] = '#d2b48c';
		}

		return $result;
	}
	public function get_reservation_list_arrival_time_by_day($sscode)
	{
		$query = DB::select(DB::expr('arrival_time as start ,reservation_no, menu_name as title,DATE_SUB(DATE_ADD(arrival_time, INTERVAL 1 HOUR),INTERVAL EXTRACT(MINUTE FROM arrival_time) MINUTE) as end,menu_code as color'))
			->from(self::$_table_name);
		$query->where('arrival_time', null, \DB::expr('IS NOT NULL'));
		$query->and_where('arrival_time', 'LIKE','%'.date('Y-m-d').'%');
		$query->and_where('reservation.sscode', $sscode);
		$result = $query->execute()->as_array();
		$colors = \constants::$colors ;
		$pit_work = \constants::$pit_work ;

		foreach ($result as $key => $value)
		{
			if($value['color'] != 'other')
			{
				$result[$key]['title'] = $pit_work[$value['color']].'(入庫)';
			}
			else
			{
				$result[$key]['title'] = $value['title'].'(入庫)';
			}

			$result[$key]['color'] = '#8B4513';
		}

		return $result;
	}
}
