<?php

class Model_Upit
{

	protected static $_table_name = 'pit';

	/*
	 * Save pit and menupit data
	 *
	 * @since 11/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function upit_save($sscode, $data)
	{
		try {

			$datapitmenu = isset($data['pitmenu']) ? $data['pitmenu'] : array();

			DB::start_transaction();
			//Get last pit_no and save pit
			$last_pit_no = Model_Pit::pit_save($sscode, $data);
			//Delete all old pitmenu before insert
			Model_Pitenablemenu::delete_all($sscode, $last_pit_no);
			//Add new record to pitmenu
			Model_Pitenablemenu::register($datapitmenu, $sscode, $last_pit_no);

			DB::commit_transaction();
		} catch (\DatabaseException $e) {
			DB::rollback_transaction();
		}
	}

	/*
	 * Del pit and menupit data
	 *
	 * @since 15/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function upit_delete($sscode, $pit_no)
	{
		try {

			DB::start_transaction();
			//Check reservation before delete
			$config = array();
			$config['where'][] = array('sscode','=',$sscode);
			$config['where'][] = array('pit_no','=',$pit_no);

			$model_reservation = new Model_Reservation();
			$query = $model_reservation->find($config);

			if(count($query) >= 1)
			{
				return 'false';
				die();
			}

			//Delete all pitmenu
			Model_Pitenablemenu::delete_all($sscode, $pit_no);
			//Delete pit
			Model_Pit::pit_delete($sscode, $pit_no);

			DB::commit_transaction();
		} catch (\DatabaseException $e) {
			DB::rollback_transaction();
			throw $e;
		}
	}
}
