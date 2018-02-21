<?php

/**
 * customer_oracle class
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 12/05/2015
 */
class Model_Customeroracle
{
	protected static $_table_name = 'reservation_personal';
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

	private static $_dbtype = 'oracle';

	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $reservation_no
	 * @return type
	 */
	public static function get_member_info_oracle($reservation_no)
	{
		return self::to_lowercase_key(
			DB::select()->from('reservation_personal')-> where('reservation_no', $reservation_no)->execute(self::$_dbtype)->as_array()
		);
	}

	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $reservation_nos is string format 123,235
	 * @return array
	 */
	public function get_list_members($reservation_nos)
	{
		$reservation_nos = explode(',', trim($reservation_nos,','));
		$result = self::to_lowercase_key(
			DB::select()->from('reservation_personal')-> where('reservation_no','IN', $reservation_nos)->execute(self::$_dbtype)->as_array()
		);
		$member = array();
		foreach ($result as $_temp)
		{
			$member[$_temp['reservation_no']] = $_temp['customer_kana'];
		}

		return $member;
	}
	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $data
	 * @param type $reservation_no
	 * @return id insert
	 */
	public function customer_save($data,$reservation_no ='')
	{
		$find = DB::select()->from('reservation_personal') -> where('reservation_no',$reservation_no)->execute(self::$_dbtype);

		if(count($find))
		{
			$rs = DB::update('reservation_personal')->set($data)->where('reservation_no','=',$reservation_no)->execute(self::$_dbtype);
			return $rs;
		}
		else
		{
			if($reservation_no && ! isset($data['reservation_no']))
			{
				$data['reservation_no'] = $reservation_no;
			}

			return self::insert('reservation_personal', $data);
		}

	}

	/**
	 * @author NamDD <namdd6566@seta-asia.com.vn>
	 * @param type $reservation_no
	 * @return true/flase
	 */
	public function customer_delete($reservation_no)
	{

		$rs = DB::delete('reservation_personal')-> where('reservation_no',$reservation_no)->execute(self::$_dbtype);
		return $rs;
	}

	private static function get_instance_oracle()
	{
		static $_oraconn = null;

		if ($_oraconn == null) {
			$db = Config::get('db');
			$dbconfig = $db[self::$_dbtype];

			$_oraconn = new PDO(
				$dbconfig['connection']['dsn'].';charset='.$dbconfig['charset'],
				$dbconfig['connection']['username'],
				$dbconfig['connection']['password']
			);
		}

		return $_oraconn;
	}

	private static function insert($table, $data)
	{
		if (isset($data['created_at']))
		{
			unset($data['created_at']);
		}

		if (isset($data['updated_at']))
		{
			unset($data['updated_at']);
		}

		$ph = str_repeat('?,', count($data));
		$sql = 'INSERT INTO '.$table.' ('.implode(',', array_keys($data)).', created_at, updated_at) VALUES ('.$ph.' current_date, current_date)';

		$sth = self::get_instance_oracle()->prepare($sql);

		$rs = $sth->execute(array_values($data));

		if ($rs == false)
		{
			throw new Exception(print_r($sth->errorInfo(), true));
		}

		return $rs;
	}

	private static function to_lowercase_key($result)
	{
		foreach ($result as &$row)
		{
			$row = array_change_key_case($row, CASE_LOWER);
		}
		return $result;
	}

}
