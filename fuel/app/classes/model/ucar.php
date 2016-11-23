<?php

/**
 * Ucar class
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 12/05/2015
 */
class Model_Ucar
{

	public function reserve($data)
	{

		try
		{
			/** created model **/
			$reservation_no  = new \Model_Reservationno();
			$car_reservation = new \Model_Carreservation();
			$cs              = new \Model_Customer();
			$cs_oracle       = new \Model_Customeroracle();
			/** get data **/
			$data_cs = $data['data_cs'];
			$data_car_reservationo = $data['data_car_reservationo'];
			$data_cs_oracle = $data['data_cs_oracle'];
			$usappy_id = $data['usappy_id'];
			$cs_card_no = $data['cs_card_no'];
			$reservation_no_info = $data['reservation_no'];
			if($usappy_id) // Call API
			{
				if($cs->save($data_cs,$usappy_id,''))
				{
					\DB::start_transaction();
					if($reservation_no_info) // Edit booking
					{
						$data_car_reservationo['reservation_no'] = $reservation_no_info;
					}
					else // Add New booking
					{
						$reservation_num = $reservation_no->create_reservationo_no('C');
						$data_car_reservationo['reservation_no'] = $reservation_num;
					}

					// Add database mysql data booking
					$car_reservation_rs = $car_reservation->car_reservation_save($data_car_reservationo,$reservation_no_info);
					\DB::commit_transaction();
					if(count($car_reservation_rs))
					{
						return true;
					}

					return false;
				}

				return false;
			}
			else
			{
				//inser info member oracle & insert car_reservtion//
				/* creat reservtionno*/
				\DB::start_transaction();

				if($reservation_no_info)
				{
					$reservation_num = $reservation_no_info;
				}
				else
				{
					$reservation_num = $reservation_no->create_reservationo_no('C');
				}

				$data_car_reservationo['reservation_no'] = $reservation_num;
				$data_cs_oracle['reservation_no'] = $reservation_num;
				$cs_oracle->customer_save($data_cs_oracle,$reservation_num);
				$car_reservation_rs = $car_reservation->car_reservation_save($data_car_reservationo,$reservation_no_info);
				\DB::commit_transaction();
				if(count($car_reservation_rs))
				{
					return true;
				}

				return false;
			}
		}
		catch(\DatabaseException $e)
		{
			\DB::rollback_transaction();
		}

	}
}
