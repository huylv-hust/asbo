<?php
/**
 * Reservation class
 * 
 * @author NamNT
 * @date 26/05/2015
 */

class Model_Repairestaff extends Fuel\Core\Model_Crud
{
	protected static $_primary_key = 'repair_staff_id';
	protected static $_table_name = 'repaire_staff';

	/**
	 * Get Repair Reservation List Data
	 * @author NamNT
	 * @since 1.0.0
	 * @param $sscode
	 * @return array $datas
	*/
	public function get_repair_staff_list($branch_code, $staff_name)
	{
		$query = DB::select('*')
				->from(self::$_table_name);
		
		$query->where('staff_name','LIKE', '%'.$staff_name.'%');
		
		if($branch_code)
		{
			$query->and_where('branch_code', $branch_code);
		}
		
		$result = $query->execute()->as_array();
		return $result;
	}
	
	public function get_repair_schedule_list($staff_id)
	{
		$query = DB::select(DB::expr('start_time as start,end_time as end'))
				->from(self::$_table_name);
		$query->and_where('repair_staff_id', $staff_id);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/**
	 * 
	 * @param type $config format array['where'][] = array('name_field','oper','value')
	 * @return type
	 */
	public function search_repair_staff_list($config)
	{
		
		$rs = static::forge()->find($config);
		if(count($rs))
		{
			return $this->object_to_array($rs);
		}
		
		return array();

	}
	
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
	
	/*
	 * List all staffs
	 *
	 * @since 27/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_staff_list($limit = null, $offset = null, $search = null)
	{
		$query = DB::select('*')
				->from(self::$_table_name);
		
		if($search['staff_name'] != null)
		{
			$query->where('staff_name', 'like', '%'.$search['staff_name'].'%');
		}
		
		if($search['branch_code'] != null)
		{
			$query->where('branch_code', $search['branch_code']);
		}
		
		if($limit)
		{
			$query->limit($limit);
		}
		
		if($offset)
		{
			$query->offset($offset);
		}
		
		return $query->execute()->as_array();
		
	}
	
	/*
	 * Save staffs
	 *
	 * @since 01/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function repairesatff_save($data, $repair_staff_id = null)
	{
		$picecount = empty($data['piece_count']) ? 0 : $data['piece_count'];
		//set array
		$db = array(			
			'branch_code' => $data['branch_code'],
			'staff_name'  => $data['staff_name'],
			'login_id'    => $data['login_id'],
			'password'    => $data['password'],
			'piece_count' => $picecount,
			'created_at'  => date('Y-m-d H:i:s'),
			'updated_at'  => date('Y-m-d H:i:s'),
		);
		
		if($repair_staff_id)//update
		{	
			$repair_staff_id = (int)$repair_staff_id;
			unset($db['created_at']);
			DB::update(self::$_table_name)
					->set($db)
					->where('repair_staff_id',$repair_staff_id)
					->execute();
			
			//last id
			$last_id = $repair_staff_id;
		}
		else //insert
		{
			$arrid = DB::insert(self::$_table_name)->set($db)->execute();
			$last_id = $arrid[0];
		}
		
		return $last_id;
		
	}
	
	/*
	 * update status staffs
	 *
	 * @since 02/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function active($status, $repair_staff_id)
	{
		//set array
		$db = array('state' => $status);
		return DB::update(self::$_table_name)
					->set($db)
					->where('repair_staff_id',$repair_staff_id)
					->execute();
		
	}
	
	/*
	 * Del staffs
	 *
	 * @since 29/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function repairestaffs_delete($repair_staff_id)
	{
		if( ! $repair_staff_id)
		{
			return false;
		}
		
		return DB::delete(self::$_table_name)
				->where('repair_staff_id', $repair_staff_id)
				->execute(); 
	}
	
	/*
	 * Get staffs info
	 *
	 * @since 01/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_staffs_info($repair_staff_id)
	{
		$result = DB::select('*')
					->from(self::$_table_name)
					->where('repair_staff_id', $repair_staff_id)
					->execute()
					->as_array();
		if($result)
		{
			return $result[0];
		}
	}
	
	/*
	 * Get unique login_id
	 *
	 * @since 01/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_staffs_unique($login_id, $repair_staff_id = null)
	{
		$query = DB::select('*')
					->from(self::$_table_name)
					->where('login_id', $login_id);
		
		if($repair_staff_id)
		{
			$query->where('repair_staff_id', '!=', $repair_staff_id);
		}
		
		$result = $query->execute()->as_array();
		
		return count($result);
	}
	
	/*
	 * Validate piece monthyear
	 *
	 * @since 15/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function validate_piece($year, $month, $pice)
	{
		foreach ($year as $k => $v)
		{
			if($v != null && $month[$k] == null)
			{
				return 'false'; 
				break;
			}
			
			if($v == null && $month[$k] != null)
			{
				return 'false'; 
				break;
			}
			
			if($v != null && $month[$k] != null){
				if($pice[$k] == null){
					return 'piece';
					break;
				}
				
				$date_input = $v.'-'.$month[$k].'-01';
				$now_date = date('Y-m').'-01';
				if(strtotime($date_input) < strtotime($now_date))
				{
					return 'false'; 
					break;
				}
			}
		}
		
		return false;
	}
	
	/*
	 * Validate piece date
	 *
	 * @since 01/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function validate_piece_year($piece)
	{
		if( ! is_array($piece))
		{
			return false;
		}
		
		foreach ($piece as $k => $v)
		{
			if($v != null && ( ! is_numeric($v) || strlen($v) != 4))
			{
				return 'false'; 
				break;
			}
		}
		
		return false;
	}
	
	/*
	 * Validate piece month
	 *
	 * @since 01/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function validate_piece_month($piece)
	{
		if( ! is_array($piece))
		{
			return false;
		}
		
		foreach ($piece as $k => $v)
		{
			if($v != null && ( ! is_numeric($v) || $v < 1 || $v > 12))
			{
				return 'false'; 
				break;
			}
		}
		
		return false;
	}
	
	/*
	 * Validate piece month
	 *
	 * @since 01/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function validate_piece_count($piece)
	{
		if( ! is_array($piece))
		{
			return false;
		}
		
		foreach ($piece as $k => $v)
		{
			if($v != null && strlen($v) > 11)
			{
				return 'length'; 
				break;
			}
			
			if($v != null && ! is_numeric($v))
			{
				return 'isnum'; 
				break;
			}
			
			if($v != null && $v == 0)
			{
				return 'rezo'; 
				break;
			}
		}
		
		return false;
	}
}
