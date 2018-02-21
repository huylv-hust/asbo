<?php

use Fuel\Core\DB;

class Model_Repairreport
{
	private static $_tax_rate = 0.08;
	protected static $_primary_key = array('repair_staff_id');
	protected static $_table_name = 'repair_report';

	public function sum_monthly($year, $month, $branch_code, $repair_staff_id = null)
	{
		$result = array(
			'price' => 0,
			'car_count' => 0,
			'piece_count_diff' => 0,
			'cancel_count' => 0,
			'over_work_min' => 0,
			'reparate' => 0,
			'work_time_per_piece' => 0,
			'work_rate' => 0,
			'price_per_piece' => 0,
			'reservation_rate' => 0,
			'price_per_car' => 0,
			'cost_rate' => 0,
		);

		$sscodes = array();

		foreach (Api::search($branch_code) as $ss)
		{
			$sscodes[] = $ss['sscode'];
		}

		if (count($sscodes) == 0)
		{
			return $result;
		}

		// report
		$report_columns = "
			sum(price) as price,
			sum(car_count) as car_count,
			sum(work_min) as work_min,
			sum(sales_piece_count - rule_piece_count) as piece_count_diff,
			sum(sales_piece_count) as sales_piece_count,
			sum(cancel_count) as cancel_count,
			sum(end_time - start_time - rest_min) as all_work_min,
			sum(
				if(end_time - start_time - rest_min <= 480, 0, end_time - start_time - rest_min - 480)
			) as over_work_min,
			sum(cost_price) as cost_price
		";

		$query = DB::select(DB::expr($report_columns))
			->from('repair_report')
			->where(DB::expr("date_format(repair_date, '%Y-%m')"), '=', sprintf('%04d-%02d', $year, $month))
			->where('work_code', '=', 1)
			->where('sscode1', 'in', $sscodes)
		;

		if (strlen($repair_staff_id)) {
			$query->where('repair_staff_id', '=', $repair_staff_id);
		}

		$rows = $query->execute()->as_array();

		if (count($rows) == 0)
		{
			return $result;
		}

		$row = $rows[0];

		$result['price'] = (float)$row['price'] / (1.0 + self::$_tax_rate);
		$result['car_count'] = (int)$row['car_count'];
		$result['piece_count_diff'] = (int)$row['piece_count_diff'];
		$result['cancel_count'] = (int)$row['cancel_count'];
		$result['over_work_min'] = (int)$row['over_work_min'];
		if ($row['work_min'] > 0) {
			$result['reparate'] = (float)$result['price'] * 60.0 / (float)$row['work_min'];
		}
		if ($row['sales_piece_count'] > 0) {
			$result['work_time_per_piece'] = $row['work_min'] / $row['sales_piece_count'];
		}
		if ($row['all_work_min'] > 0) {
			$result['work_rate'] = (float)$row['work_min'] / (float)$row['all_work_min'];
		}
		if ($row['sales_piece_count'] > 0) {
			$result['price_per_piece'] = $row['price'] / $row['sales_piece_count'];
		}
		if ($result['car_count'] > 0) {
			$result['price_per_car'] = (float)$result['price'] / (float)$result['car_count'];
		}
		if ($result['price'] > 0) {
			$result['cost_rate'] = (float)$row['cost_price'] / (float)$result['price'];
		}

		// plan
		$plan_sql = "
			select
				sum(p.piece_count) as plan_pirce_total
			from
				repair_staff_plan as p
				inner join repaire_staff as s on (
					p.repair_staff_id = s.repair_staff_id
				)
			where
				p.year = :year and
				p.month = :month and
				s.branch_code = :branch_code
		";
		if (strlen($repair_staff_id)) {
			$plan_sql .= 'and p.repair_staff_id = :repair_staff_id';
		}

		$plan_query = DB::query($plan_sql)
			->bind('year', $year)
			->bind('month', $month)
			->bind('branch_code', $branch_code)
		;

		if (strlen($repair_staff_id)) {
			$plan_query->bind('repair_staff_id', $repair_staff_id);
		}

		$plan_pirce_total = $plan_query->execute()->get('plan_pirce_total');

		// reservation
		$reservation_columns = "
			sum(a_piece_count + b_piece_count) as reserve_piece_total
		";

		$reservation_query = DB::select(DB::expr($reservation_columns))
			->from('repair_reservation')
			->where(DB::expr("date_format(arrival_time, '%Y-%m')"), '=', sprintf('%04d-%02d', $year, $month))
			->where('sscode', 'in', $sscodes)
		;

		if (strlen($repair_staff_id)) {
			$reservation_query->where('repair_staff_id', '=', $repair_staff_id);
		}

		$reserve_piece_total = $reservation_query->execute()->get('reserve_piece_total');

		if ($plan_pirce_total > 0) {
			$result['reservation_rate'] = (float)$reserve_piece_total / (float)$plan_pirce_total;
		}

		return $result;
	}
}
