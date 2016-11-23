<?php

class Model_Uopentimer
{

	protected static $_table_name = 'open_timer';

	/*
	 * Save menu setting
	 *
	 * @since 30/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function uopentimer_save($sscode, $data)
	{
		try {

			DB::start_transaction();
			
			//save open timer
			$last_id = Model_Opentimer::opentimer_savedata($sscode, $data);
			//deletel all open_time_detail
			Model_Opentimerdetail::delete_all($last_id);
			//register new record to open_time_detail
			Model_Opentimerdetail::opentimedetail_save($last_id, $data);
			
			DB::commit_transaction();
		} catch (\DatabaseException $e) {
			DB::rollback_transaction();
			throw $e;
		}
	}
	
	/*
	 * Del Opentimer && openttimerdetail
	 *
	 * @since 30/06/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function uopentimer_delete($open_timer_id)
	{
		try {
			DB::start_transaction();
			
			//delete open_timer_detail
			Model_Opentimerdetail::delete_all($open_timer_id);
			//delete open_timer
			Model_Opentimer::opentimer_delete($open_timer_id);
			
			DB::commit_transaction();
		} catch (\DatabaseException $e) {
			DB::rollback_transaction();
		}
	}
}
