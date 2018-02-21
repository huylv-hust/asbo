<?php

namespace Car;
use \Model\Car;
use \Model\Carreservation;

class Controller_Cars extends \Controller_Usappy
{

	/**
	 * List Car
	 * @author NamNT
	 * @since 1.0.0
	 * @param
	 * @return List Car
	*/
	public function action_index()
	{
		$model = new \Model_Car();
		$config = ['pagination_url' => \Uri::base().'car/cars/index',
			'total_items'    => count($model->get_car_list(\Cookie::get('sscode'))),
			'per_page'       => \Constants::$per_page,
			'uri_segment'    => 4,
			'num_links'      => \Constants::$num_links,
			'link_offset'    => 1
			];
		$pagination = \Pagination::forge('mypagination', $config);
		$data['sscode']  = \Cookie::get('sscode');
		$data['ssname']  = \Cookie::get('ss_name');
		$data['listCar'] = \Model_Car::find('all',
			[
				'limit'  => $pagination->per_page,
				'offset' => $pagination->offset,
				'where'  => array(
					array('sscode', \Cookie::get('sscode')),
				),
			]
		);

		$this->template->title = 'Usappyオートサービス管理';
		$this->template->content = \View::forge('cars/car', $data);
		$this->template->content->ssfinder = \View::forge('partials/ssfinder');
	}

	/**
	* Save Car Data
	* @author NamNT
	* @since 1.0.0
	* @param
	* @return
	*/
	public function action_save_car()
	{
		$car_name   = \Input::param('car_name');
		$plate_no   = \Input::param('plate_no');
		$car_id     = \Input::param('car_id');
		$sscode     = \Cookie::get('sscode');
		$created_at = date('Y-m-d H:i:s');
		$updated_at = date('Y-m-d H:i:s');

		$data = compact('car_name', 'plate_no', 'sscode','updated_at');
		$model = new \Model_Car();
		if((int)$car_id != -1)
		{
		    $model = $model->find($car_id);
		}
		else
		{
		    $data['created_at'] = $created_at ;
		}

		$model->set($data);
		$model->save();

		\Response::redirect_back(\Uri::base());
	}

	/**
	 * Save Car Detail
	 * @author NamNT
	 * @since 1.0.0
	 * @param $car_id
	 * @return Detail a car
	*/
	public function action_detail_car()
	{
		$car    = new \Model_Car();
		$car_id = \Input::param('id');
		$data   = $car->get_car_info((int) $car_id);
		$content_type = array('Content-type' => 'application/json');
		return new \Response(json_encode($data), 200, $content_type);
	}

	/**
	 * Delete Car
	 * @author NamNT
	 * @since 1.0.0
	 * @param $car_id
	 * @return List Car
	*/
	public function action_delete_car()
	{
		$sscode = \Cookie::get('sscode');
		$id     = \Input::param('id');
		$carreservation = new \Model_Carreservation;
		if (count($carreservation->get_reservation_info($id)))
		{
			$content_type = array(
				'Content-type' => 'application/json',
				'SUCCESS'      => 0,
			);
			return new \Response(json_encode(null), 200, $content_type);
		}
		else
		{
			$result = \DB::delete('car')->where('car_id', '=', \Input::param('id'))->execute();
			exit();
		}
	}
}
