<?php

/**
 * carreservation class
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 12/05/2015
 */
class Model_Reservationno extends Fuel\Core\Model_Crud
{
	protected static $_table_name = 'reservation_no';
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
	 * 
	 * @param type $ident
	 * @return reservationo_num
	 */
	public function create_reservationo_no($ident)
	{
		$data = array(
			'reservation_date' => date('Y-m-d',time()),
			'ident'            => $ident,
			'created_at'       => date('Y-m-d H:i:s',time()),
			'updated_at'       => date('Y-m-d H:i:s',time()),
		);
		$reservation_no = static::forge();
		$reservation_no->set($data);
		$rs = $reservation_no->save();
		$reservationo_num = $ident.date('Ymd',time()).'-'.str_pad($rs['0'],4,'0', STR_PAD_LEFT);
		return $reservationo_num;
	}

}
