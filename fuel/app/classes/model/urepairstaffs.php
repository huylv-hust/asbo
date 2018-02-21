<?php

class Model_Urepairstaffs
{

	protected static $_table_name = 'repaire_staff';

	/*
	 * Save repair staffs data
	 *
	 * @since 29/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function urepairstaffs_save($data, $repair_staff_id)
	{
		try {
			DB::start_transaction();

			//Repair staff save
			$last_id = Model_Repairestaff::repairesatff_save($data, $repair_staff_id);
			
			//Pice coutn delete
			Model_Picecount::picecount_delete($last_id);
			
			//Pice coutn save
			Model_Picecount::register($data, $last_id);
			
			DB::commit_transaction();
		} catch (\DatabaseException $e) {
			DB::rollback_transaction();
		}
	}
	
	/*
	 * Del staff and pice_count
	 *
	 * @since 29/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */
	public function urepairstaffs_delete($repair_staff_id)
	{
		try {

			DB::start_transaction();
			
			//Check repair schedule before delete
			$query = Model_Repairschedule::query()->where('repair_staff_id', $repair_staff_id);
			$count = $query->max('repair_staff_id');
			if($count >= 1)
			{
				return 'false';
			}
			
			//delet all picecount by staff_id
			Model_Picecount::picecount_delete($repair_staff_id);
			//delete repaire staffs
			Model_Repairestaff::repairestaffs_delete($repair_staff_id);

			DB::commit_transaction();
		} catch (\DatabaseException $e) {
			DB::rollback_transaction();
			throw $e;
		}
	}
}
