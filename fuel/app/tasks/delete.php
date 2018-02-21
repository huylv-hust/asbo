<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Fuel\Tasks;

class Delete
{
	public static function run($speech = null)
	{
		\Config::set('log_threshold', \Fuel::L_INFO);

		$sql_car_reservation = 'SELECT reservation_no FROM car_reservation WHERE end_time < DATE_ADD(CURDATE(),INTERVAL -89 DAY)';
		$sql_reservation = 'SELECT reservation_no FROM reservation WHERE end_time < DATE_ADD(CURDATE(),INTERVAL -89 DAY)';
		$sql_repair_reservation = 'SELECT reservation_no FROM repair_reservation WHERE arrival_time < DATE_ADD(CURDATE(),INTERVAL -89 DAY)';

		$res_car = \Fuel\Core\DB::query($sql_car_reservation)->execute()->as_array();
		foreach ($res_car as $row)
		{
			\Fuel\Core\Log::info('DELETE car_reservation:' . $row['reservation_no']);
			\Fuel\Core\DB::delete('car_reservation')->where('reservation_no', '=', $row['reservation_no'])->execute();
			\Fuel\Core\DB::delete('reservation_personal')->where('reservation_no', '=', $row['reservation_no'])->execute('oracle');
		}

		$res_work = \Fuel\Core\DB::query($sql_reservation)->execute()->as_array();
		foreach ($res_work as $row)
		{
			\Fuel\Core\Log::info('DELETE reservation:' . $row['reservation_no']);
			\Fuel\Core\DB::delete('reservation')->where('reservation_no', '=', $row['reservation_no'])->execute();
			\Fuel\Core\DB::delete('reservation_personal')->where('reservation_no', '=', $row['reservation_no'])->execute('oracle');
		}

		$res_repair = \Fuel\Core\DB::query($sql_repair_reservation)->execute()->as_array();
		foreach ($res_repair as $row)
		{
			\Fuel\Core\Log::info('DELETE repair_reservation:' . $row['reservation_no']);
			\Fuel\Core\DB::delete('repair_reservation')->where('reservation_no', '=', $row['reservation_no'])->execute();
			\Fuel\Core\DB::delete('reservation_personal')->where('reservation_no', '=', $row['reservation_no'])->execute('oracle');
		}
	}
}
