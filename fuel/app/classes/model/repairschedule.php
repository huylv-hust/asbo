<?php
/**
 * Repair Schedule class
 * 
 * @author NamNT
 * @date 26/05/2015
 */

class Model_Repairschedule extends \Orm\Model
{
	protected static $_primary_key = array('repair_schedule_id');
	protected static $_table_name = 'repair_schedule';

	/**
	 * Get repair schedule List Data
	 * @author NamNT
	 * @since 1.0.0
	 * @param $sscode
	 * @return array $datas
	*/
	
	public function get_repair_schedule_list($staff_id)
	{
		$query = DB::select(DB::expr('start_time as start,end_time as end,sscode,repair_schedule_id'))
				->from(self::$_table_name);
		$query->and_where('repair_staff_id', $staff_id);
		$result = $query->execute()->as_array();
		$list_ss_info = \api::get_ss_name() ;
		$ss = array();
		foreach ($list_ss_info as $key => $value)
		{
			$ss[$value['sscode']] = $value['ss_name'];
		}
		
		foreach ($result as $key => $value)
		{
			$ssname = '';
			if(isset($ss[$value['sscode']]))
			{
				$ssname = $ss[$value['sscode']];
			}

			$result[$key]['title'] = $ssname;
		}
		
		return $result;
	}
	
	public function get_repair_schedule_detail($id)
	{
		$query = DB::select(DB::expr('repair_schedule_id,staff_name as name,repair_schedule.repair_staff_id as staff_id,start_time,end_time,sscode'))
				->from(self::$_table_name)
				->join('repaire_staff')
				->on('repair_schedule.repair_staff_id', '=', 'repaire_staff.repair_staff_id');
		$query->and_where('repair_schedule_id', $id);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	/**
	 * @author NamNT
	 * @param type $repair_schedule_id
	 * @return 
	 */
	public function repair_schedule_delete($repair_schedule_id)
	{
		$repair_schedule = static::forge()->find($repair_schedule_id);
		return $repair_schedule->delete();
	}
}
