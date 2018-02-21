<?php

class Model_Pit extends \Orm\Model
{

	protected static $_primary_key = array(
		'sscode',
		'pit_no',
	);
	protected $validation;
	protected static $_properties = array(
		'sscode',
		'pit_no',
		'pit_name',
		'note',
		'is_public',
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
	protected static $_table_name = 'pit';

	/*
	 * List all pit by sscode
	 *
	 * @since 11/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_pit_list($sscode, $limit = null, $offset = null)
	{
		$query = DB::select('*')
				->from(self::$_table_name)
				->where('sscode', $sscode);
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
	 * List pit info by sscode and pit_no
	 *
	 * @since 11/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function get_pit_info($sscode, $pit_no)
	{
		return DB::select('*')
					->from(self::$_table_name)
					->where('sscode', $sscode)
					->and_where('pit_no', $pit_no)
					->execute()
					->as_array();
	}

	/*
	 * Save data
	 *
	 * @since 11/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function pit_save($sscode, $data)
	{
		$pit_no = (int)$data['pit_no'];
		$db = array(
			'pit_no'     => $pit_no,
			'sscode'     => $sscode,
			'pit_name'   => $data['pit_name'],
			'is_public'  => $data['is_public'],
			'note'       => $data['note'],
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		);

		if($pit_no == 0) //insert
		{
			$arrid = DB::insert(self::$_table_name)->set($db)->execute();
			$last_id = $arrid[0];
		}else // update
		{
			unset($db['created_at']);
			$last_id = $pit_no;
			DB::update(self::$_table_name)->set($db)->where('pit_no',$pit_no)->and_where('sscode',$sscode)->execute();
		}

		return $last_id;
	}

	/*
	 * Delete data
	 *
	 * @since 11/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public static function pit_delete($sscode, $pit_no)
	{
		return DB::delete(self::$_table_name)
				->where('sscode', $sscode)
				->and_where('pit_no', $pit_no)
				->execute();

	}
	public function search_pit_list($sscode,$pit_no)
	{
		if( ! count($sscode) OR ! count($pit_no))
		{
			return array();
		}

		return DB::select('*')
					->from(self::$_table_name)
					->where('sscode', "IN" , $sscode)
					->and_where('pit_no',"IN", $pit_no)
					->execute()
					->as_array();

	}

}
