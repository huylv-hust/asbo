<?php
use Fuel\Core\DB;

class Model_Repaireventplan extends \Orm\Model
{
	private static $_tax_rate = 0.08;
	protected static $_primary_key = array('event_id');
	protected static $_table_name = 'repair_event_plan';

	public function get_list($sscode,$pagination = null,$year)
	{
		$limit = '';
		if($pagination)
		{
			$limit = "LIMIT $pagination->per_page OFFSET $pagination->offset";
		}

		$query1 = DB::query("SELECT `repair_event_plan`.* , SUM(repair_report.price) as price
			FROM `repair_event_plan`
			LEFT JOIN `repair_report`
			ON (`repair_report`.`repair_date` >= `repair_event_plan`.`start_date`
			AND `repair_report`.`repair_date` <= `repair_event_plan`.`end_date`)
			AND (`repair_report`.`sscode1` = '".$sscode."' OR `repair_report`.`sscode2` = '".$sscode."')

			WHERE `repair_event_plan`.`sscode` = '".$sscode."'
			AND DATE_FORMAT(start_date,'%Y') = '".$year."'
			AND ((DATE_FORMAT(start_date,'%Y') < '".$year."' AND DATE_FORMAT(end_date,'%Y') > '".$year."')
			OR (DATE_FORMAT(end_date,'%Y') = '".$year."'))
			GROUP BY `event_id`  ORDER BY `start_date` , `event_id` ASC ".
			$limit
		);


		$result1 = $query1->execute()->as_array();

		$query2 = DB::query("SELECT `repair_event_plan`.* , SUM(repair_reservation.a_piece_count) as piece_count1,SUM(repair_reservation.b_piece_count) as piece_count2,
			SUM(
				IF(
					date_format(repair_reservation.created_at, '%Y%m%d') < date_format(repair_event_plan.start_date, '%Y%m%d'),
					repair_reservation.price,
					0
				)
			) as before_sales,
			SUM(repair_reservation.price) as reserve_sales
			FROM `repair_event_plan`
			LEFT JOIN `repair_reservation`
			ON (`repair_reservation`.`arrival_time` >= `repair_event_plan`.`start_date`
			AND `repair_reservation`.`arrival_time` <  DATE_ADD(`repair_event_plan`.`end_date`, INTERVAL 1440 MINUTE))
			AND (`repair_reservation`.`sscode` = '".$sscode."')

			WHERE `repair_event_plan`.`sscode` = '".$sscode."'
			AND DATE_FORMAT(start_date,'%Y') = '".$year."'
			AND ((DATE_FORMAT(start_date,'%Y') < '".$year."' AND DATE_FORMAT(end_date,'%Y') > '".$year."')
			OR (DATE_FORMAT(end_date,'%Y') = '".$year."'))
			GROUP BY `event_id`  ORDER BY `start_date` ASC ".
			$limit
		);
		$result2 = $query2->execute()->as_array();
		$rs = array();

		foreach($result2 as $key => $value)
		{
			$rs[$value['event_id']]['piece_count1'] = $value['piece_count1'];
			$rs[$value['event_id']]['piece_count2'] = $value['piece_count2'];
			$rs[$value['event_id']]['before_sales'] = $value['before_sales'];
			$rs[$value['event_id']]['reserve_sales'] = $value['reserve_sales'];
		}

		foreach($result1 as $key => $value)
		{
			if(isset($rs[$value['event_id']]))
			{
				$result1[$key]['piece_count1'] = $rs[$value['event_id']]['piece_count1'];
				$result1[$key]['piece_count2'] = $rs[$value['event_id']]['piece_count2'];
				$result1[$key]['before_sales'] = $rs[$value['event_id']]['before_sales'];
				$result1[$key]['reserve_sales'] = $rs[$value['event_id']]['reserve_sales'];
			}
			else
			{
				$result1[$key]['piece_count1'] = 0;
				$result1[$key]['piece_count2'] = 0;
				$result1[$key]['before_sales'] = 0;
				$result1[$key]['reserve_sales'] = 0;
			}
		}

		return $result1;
	}

	public function get_event_info($event_id)
	{
		$query = DB::select('*')
			->from(self::$_table_name)
			->where('event_id', '=', $event_id);
		$result = $query->execute()->as_array();

		return $result[0];
	}


	public function get_events($branch_code = null)
	{
		$where = '';
		$sscodes = array();

		if (strlen($branch_code))
		{
			foreach (Api::search($branch_code) as $ss)
			{
				$sscodes[] = $ss['sscode'];
			}

			if (count($sscodes) == 0)
			{
				return array();
			}

			$where = 'WHERE sscode IN :sslist';
		}

		$sql = "
			SELECT * FROM `repair_event_plan`
			$where
			ORDER BY start_date DESC
		";

		$query = DB::query($sql);

		if (strlen($where))
		{
			$query->bind('sslist', $sscodes);
		}

		return $query->execute()->as_array();
	}

	public function sum($branch_code, $event_id, $repair_staff_id = null)
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

		// term
		$events = DB::select()->from('repair_event_plan')->where('event_id', '=', $event_id)->execute()->as_array();
		if (count($events) == 0)
		{
			return $result;
		}

		$event = $events[0];


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
			sum(cost_price) as cost_price,
			count(distinct repair_staff_id) as staff_count
		";

		$query = DB::select(DB::expr($report_columns))
			->from('repair_report')
			->where('repair_date', '>=', $event['start_date'])
			->where('repair_date', '<=', $event['end_date'])
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
		$result['cancel_count'] =(int) $row['cancel_count'];
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

		$result['staff_count'] = $row['staff_count'];

		// plan
		$plan_pirce_total = $event['piece_count'];

		// reservation
		$reservation_columns = "
			sum(a_piece_count + b_piece_count) as reserve_piece_total
		";

		$reservation_query = DB::select(DB::expr($reservation_columns))
			->from('repair_reservation')
			->where('arrival_time', '>=', $event['start_date'])
			->where('arrival_time', '<=', $event['end_date'])
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
