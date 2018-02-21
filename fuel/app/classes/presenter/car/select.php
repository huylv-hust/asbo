<?php
/**
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 19/05/2015
 */
class Presenter_Car_Select extends Presenter
{
	public function view()
	{
		$row = $this->obj;
		$data = Constants::show_car_info($data, $row);
		if(Fuel\Core\Input::method() == 'POST')
		{
			if(Fuel\Core\Input::param('check_model_code') == '1')
			{
				$this->check_model_code = 'checked="checked"';
			}
			else
			{
				$this->check_model_code = '';
			}
		}
		else
		{

			if((int)$row['car_model_code'] == 0 && Fuel\Core\Input::get('reservation_no'))
				$this->check_model_code = 'checked="checked"';
			else
			{
				if($row['car_model_code'] > 0)
					$this->check_model_code = 'disabled="true"';
				else
					$this->check_model_code = '';
			}

		}

		$this->car_maker_code = $data['car_maker_code'];
		$this->car_model_code = isset($data['car_model_code']) ? str_replace('<option value="0"></option>','',$data['car_model_code']) : '';
		$this->car_year = isset($data['car_year']) ? str_replace('<option value="0"></option>','',$data['car_year']) : '';
		$this->car_month = \Constants::select('month','car_month',$row['car_month'],array('class' => 'form-control'));
		$this->car_type_code = isset($data['car_type_code']) ? str_replace('<option value="0"></option>','',$data['car_type_code']) : '';
		$this->car_grade_code = isset($data['car_grade_code']) ? str_replace('<option value="0"></option>','',$data['car_grade_code']) : '';
	}
}
