<?php
/**
 * Reservation class
 *
 * @author NamNT
 * @date 22/05/2015
 */

class Model_Repairreservation extends Fuel\Core\Model_Crud
{
	protected static $_primary_key = 'reservation_no';
	protected static $_table_name = 'repair_reservation';

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
	 * Get Repair Reservation List Data
	 * @author NamNT
	 * @since 1.0.0
	 * @param $sscode
	 * @return array $datas
	*/
	public function get_repair_reservation_list($sscode)
	{
		//try
		//{
		//	return json_decode(\Fuel\Core\Cache::get('get_repair_reservation_list'.$sscode),true);
		//}
		//catch (\CacheNotFoundException $e)
		//{
		$query = DB::select(DB::expr('arrival_time as start,reservation_no , return_time ,return_time as end,car_maker_code,car_model_code,a_piece_count,b_piece_count,is_shuttle_request,is_car_request'))
		   ->from(self::$_table_name);
		$query->and_where('repair_reservation.sscode', $sscode);
		$result = $query->execute()->as_array();
		$colors = \constants::$colors ;
		$list_maker = \Api::get_list_maker();
		$listMk = array();
		$listMd = array();

		foreach($list_maker as $key => $value){
			$listMk[$value['maker_code']] = $value['maker'];
		}

		foreach ($result as $key => $value)
		{
			$maker_name = '';
			$model_name = '';
			$list_model = \Api::get_list_model($value['car_maker_code']);
			//var_dump($value['car_model_code']);
			foreach($list_model as $_temp)
			{
				if(isset($_temp['model_code']) && $_temp['model_code'] == $value['car_model_code'])
				{
					$model_name = $_temp['model'];
					break;
				}

			}

			if(isset($listMk[$value['car_maker_code']]))
			{
				$maker_name = $listMk[$value['car_maker_code']];
			}

			//$result[$key]['title'] = $maker_name.' '.$model_name.' '.'A:'.$value['a_piece_count'].' '.'B:'.$value['b_piece_count'];

			$result[$key]['title'] = '';
			if($value['is_car_request'] == 1)
				$result[$key]['title'] .= '代';

			if($value['is_shuttle_request'] == 1)
				$result[$key]['title'] .= '送';

			$result[$key]['title'] .= date('Hi',  strtotime($value['start']));

			if($value['return_time'])
				$result[$key]['title'] .= date('Hi',strtotime($value['return_time']));

			$result[$key]['title'] .= '_A'.$value['a_piece_count'].'B'.$value['b_piece_count'].'_'.$model_name;
			$result[$key]['title'] = trim($result[$key]['title'],'_');
			$return_time = explode(' ', $result[$key]['end']);
			$start_time = explode(' ', $result[$key]['start']);
			$range = strtotime($return_time[0]) - strtotime($start_time[0]);

			if($range < 0){
				$range = strtotime($start_time[0]) - strtotime($return_time[0]);
			}

			if(!$result[$key]['end']){
				$result[$key]['color'] = '#FF0000';
			}
			else if($range / 3600 == 0)
			{
				$result[$key]['color'] = '#0000FF';
			}
			else if($range / 3600 == 24)
			{
				$result[$key]['color'] = '#008000';
			}
			else if($range / 3600 == 48)
			{
				$result[$key]['color'] = '#9400D3';
			}
			else
			{
				$result[$key]['color'] = '#FF8C00';
			}
		}

		//\Fuel\Core\Cache::set('get_repair_reservation_list'.$sscode, json_encode($result),300);

		return $result;
		//}

	}
	/**
	 * Get Repair Reservation List Data By Day
	 * @author NamNT
	 * @since 1.0.0
	 * @param $sscode
	 * @return array $datas
	*/
	public function get_repair_reservation_list_by_day($sscode)
	{
		$query = DB::select(DB::expr('arrival_time as start,reservation_no ,return_time as end'))
				->from(self::$_table_name);
		$query->and_where('repair_reservation.sscode', $sscode);
		$query->where('arrival_time', 'LIKE','%'.date('Y-m-d').'%');
		$result = $query->execute()->as_array();
		$colors = \constants::$colors ;

		foreach ($result as $key => $value)
		{
			$result[$key]['title'] = 'リペア';
			$result[$key]['color'] = $colors['repair'];
			$result[$key]['type'] = 'repair';
		}

		return $result;
	}
	/**
	 * author NamDD
	 * @param type $config
	 * @return type
	 */

	public function  search_repair_reservation_list($config)
	{
		$rs = static::forge()->find($config);
		if(count($rs))
		{
			return $this->object_to_array($rs);
		}

		return array();

	}
	/**
	  * author NamDD
	 * @param type $reservation_no
	 * @return boolean
	 */
	public function repair_reservation_delete($reservation_no)
	{
		$rs = static::forge()->find_by_pk($reservation_no);
		if(count($rs))
		{
			return $rs->delete();
		}

		return false;
	}
	/**
	 * author NamDD
	 * @param type $data
	 * @param type $reservation_no
	 * @return array
	 */
	public function repair_reservation_save($data, $reservation_no = '')
	{
		if ( ! count($data))
			return array();

		$data['updated_at'] = date('Y-m-d H:i:s');
		if ($reservation_no == '')
		{
			$data['created_at'] = date('Y-m-d H:i:s');
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
	 * author NamDD
	 * @param type $reservation_no
	 * @return type
	 */
	public function get_repair_reservation_info($reservation_no)
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
	 * author NamDD
	 * @param type $config
	 * @return total
	 */
	public function sum($field, $config)
	{
		$wh = '';
		foreach($config as $_config)
		{
			if(is_array($_config['2']))
			{
				$_values = implode(',', $_config['2']);
				$wh .= $_config['0'].' '.$_config['1'].' ( '.$_values.' ) AND ';
			}
			else
			{
				$wh .= $_config['0'].$_config['1'].'"'.$_config['2'].'" AND ';
			}
		}

		$wh = trim($wh,' AND ');
		if($wh)
		{
			$wh = ' WHERE '.$wh;
		}

		$rs = DB::query('SELECT SUM('.$field.')	AS '.$field.' FROM repair_reservation'.$wh)->execute()->as_array();
		if(count($rs))
		{
			return $rs['0'][$field];
		}

		return -1;
	}
	/**
	 *
	 * @param type $data
	 * @return array
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
}
