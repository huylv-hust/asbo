<?php

class Model_Umenusetting
{

	protected static $_table_name = 'menu_setting';

	/*
	 * Save menu setting
	 *
	 * @since 22/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function umenusetting_save($sscode, $data)
	{
		try {

			DB::start_transaction();
			
			$menu_code = trim($data['menu_code']);
			
			//save to menu_setting
			Model_Menusetting::menusetting_save($sscode, $data);
			
			//save open timer where is_holiday = 0
			$last_id = Model_Opentimer::opentimer_save($sscode, $data);
			//deletel all open_time
			Model_Opentimerdetail::delete_all($last_id);
			//register new record to open_time
			Model_Opentimerdetail::register($last_id, $data);
			
			//save open timer where is_holiday = 1
			$last_id = Model_Opentimer::opentimer_save($sscode, $data, 1);
			//deletel all open_time
			Model_Opentimerdetail::delete_all($last_id);
			//register new record to open_time
			Model_Opentimerdetail::register($last_id, $data, 'holiday');
			
			//delete all stop_date
			Model_Stopdate::delete_all($sscode, $menu_code);
			//register new record to stop_date
			Model_Stopdate::register($data, $sscode);
			
			if($data['menu_code'] === 'coating')
			{
				//delete all enable_coating
				Model_Enablecoating::delete_all($sscode);
				//register new record to enable_coating
				Model_Enablecoating::register($data, $sscode);
			}
			
			DB::commit_transaction();
		} catch (\DatabaseException $e) {
			DB::rollback_transaction();
			throw $e;
		}
	}
	
	/*
	 * Del pit and menupit data
	 *
	 * @since 15/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function umenusetting_delete($sscode, $menu_code)
	{
		try {

			DB::start_transaction();
			DB::commit_transaction();
		} catch (\DatabaseException $e) {
			DB::rollback_transaction();
		}
	}
}
