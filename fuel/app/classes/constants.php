<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Constants
{
	static $branch = array(
		'202480' => '東日本宇佐美　北海道支店',
		'283963' => '東日本宇佐美　東北支店',
		'326855' => '東日本宇佐美　上信越支店',
		'458926' => '東日本宇佐美　東京販売支店',
		'458935' => '東日本宇佐美　神奈川販売支店',
		'458944' => '東日本宇佐美　埼玉栃木販売支店',
		'458953' => '東日本宇佐美　千葉茨城販売支店',
		'451987' => '西日本宇佐美　東海支店',
		'457142' => '西日本宇佐美　中部支店',
		'513545' => '西日本宇佐美　北陸支店',
		'575167' => '西日本宇佐美　関西支店',
		'603074' => '西日本宇佐美　山陽支店',
		'784351' => '西日本宇佐美　九州支店',
		'458720' => 'ダイツー',
	);
	static $pit_work = array(
		'inspection' => '車検',
		'coating'    => 'コーティング',
		'wash'       => '洗車',
		'oil'        => 'オイル',
		'tire'       => 'タイヤ',
		'other'      => 'その他',
		'repair'     => 'リペア',
	);
	static $coating_code = array(
		'none'    => '',
		'crystal' => 'クリスタルキーパー',
		'diamond' => ' ダイヤモンドキーパー',
		'double'  => 'ダブルダイヤキーパー',
		'pure'    => 'ピュアキーパー',
	);
	static $car_color = array(
		'0'  => '',
		'1'  => 'パールホワイト',
		'2'  => 'ベージュ',
		'3'  => 'シルバー',
		'4'  => 'グレー',
		'5'  => 'ガンメタ',
		'6'  => 'ピンク',
		'7'  => 'ワインレッド',
		'8'  => 'オレンジ',
		'9'  => 'ゴールド',
		'10' => 'グリーン',
		'11' => 'マジョーラ',
		'12' => '白',
		'13' => '黒',
		'14' => '紫',
		'15' => '赤',
		'16' => '黄',
		'17' => '茶',
		'18' => '水色',
		'19' => '青',
		'20' => '紺',
		'99' => 'その他',
	);
	static $car_size = array(
		'0' => '',
		'1' => '軽自動車（乗用・貨物）',
		'2' => '普通乗用車 5・7ナンバー',
		'3' => '普通乗用車 3ナンバー',
		'4' => '小型貨物 4ナンバー',
		'5' => '普通貨物 1ナンバー',
	);
	static $car_weight = array(
		'0' => '',
		'1' => '501kg～1000kg',
		'2' => '1001kg～1500kg',
		'3' => '1501kg～2000kg',
		'4' => '2001kg～2500kg',
		'5' => '2501kg～3000kg',
	);
	static $car_weight_1 = array(
		'0' => '',
		'1' => '軽自動車',
	);
	static $car_weight_2 = array(
		'0' => '',
		'1' => '1000kg',
		'2' => '1500kg',
		'3' => '2000kg',
		'4' => '2500kg',
		'5' => '3000kg',
	);
	static $car_weight_3 = array(
		'0' => '',
		'1' => '1000kg',
		'2' => '1500kg',
		'3' => '2000kg',
		'4' => '2500kg',
		'5' => '3000kg',
	);
	static $car_weight_4 = array(
		'0' => '',
		'1' => '車両重量～2000kg',
		'2' => '車両重量～2500kg',
		'3' => '車両重量～3000kg',
		'4' => '車両重量～4000kg',
	);
	static $car_weight_5 = array(
		'0' => '',
		'1' => '車両重量～2000kg',
		'2' => '車両重量～2500kg',
		'3' => '車両重量～3000kg',
		'4' => '車両重量～4000kg',
	);

	static $is_car_request = array(
		'0' => '無し',
		'1' => '有り',
	);
	static $is_car_request_oil_tire_wash = array(
		''  => '',
		'0' => '無し',
		'1' => '有り',
	);
	static $wheel_preparation_code = array(
		'0' => '',
		'1' => '有り',
		'2' => '無し',
	);
	static $tire_size_code = array(
		''   => '',
		'12' => '12インチ',
		'13' => '13インチ',
		'14' => '14インチ',
		'15' => '15インチ',
		'16' => '16インチ',
		'17' => '17インチ',
		'99' => '不明',
	);
	static $tire_preparation_code = array(
		'0' => '',
		'1' => '購入予定',
		'2' => '交換作業',
	);
	static $state = array(
		'0' => '実施待ち',
		'1' => '完了',
		'2' => 'キャンセル',
	);
	static $colors = array(
		'oil'        => '#008000',
		'tire'       => '#696969',
		'inspection' => '#ff4500',
		'wash'       => '#4169e1',
		'coating'    => '#0000ff',
		'other'      => '#8b4513',
		'repair'     => '#d2b48c',
	);
	static $month = array(
		'0'  => '月を選択して下さい',
		'1'  => '1月',
		'2'  => '2月',
		'3'  => '3月',
		'4'  => '4月',
		'5'  => '5月',
		'6'  => '6月',
		'7'  => '7月',
		'8'  => '8月',
		'9'  => '9月',
		'10' =>	'10月',
		'11' => '11月',
		'12' => '12月',
	);
	static $purpose_list = array(
		''           => '用途を選択してください',
		'repair'     => 'リペア',
		'coating'    => 'コーティング',
		'inspection' => '車検',
		'other'      => 'その他',
	);

	static $is_shuttle_request = array(
		''  => '',
		'0' => '無し',
		'1' => '有り',
	);
	public static $url_loged = '/';
	public static $per_page = 10;
	public static $num_links = 10;

	/**
	 * select
	 * show contstants format input select html
	 *
	 * @param type $name_constants
	 * @param type $field name of tag select
	 * @param type $attributes form css
	 * @return html
	 */
	public static function select($name_constants, $field = '', $value = null, $attributes = array())
	{
		if($name_constants == 'pit_work')
		{
			unset(Constants::$pit_work['repair']);
		}

		if ($field == '')
			return Form::select($name_constants, $value,Constants::$$name_constants,$attributes,array());
		return Form::select($field, $value, Constants::$$name_constants, $attributes, array());
	}
	/**
	 *
	 * @param type $array
	 * @param type $key
	 * @param type $value
	 * @param type $field
	 * @param type $selected
	 * @param type $attributes
	 * @return select
	 */
	public static function array_to_select($array,$key,$value,$field='',$selected = '', $attributes = array())
	{
		$_array = array();
		if($key)
		{
			foreach ($array as $_temp)
			{
				$_array[$_temp[$key]] = strip_tags(htmlspecialchars($_temp[$value]));
			}

			return Form::select($field, $selected,$_array , $attributes, array());
		}

		return Form::select($field, $selected,$array , $attributes, array());

	}
	/**
	 *
	 * @param type $array
	 * @param type $key
	 * @param type $value
	 * @param type $selected
	 * @return string
	 */
	public static function array_to_option($array,$key,$value,$selected = '',$default = true)
	{
		$option = '<option value=""></option>';
		if($default)
		{
			$option = '<option value="0"></option>';
		}

		$_array = array();
		foreach ($array as $_temp)
		{
			$_array[$_temp[$key]] = strip_tags(htmlspecialchars($_temp[$value]));
		}

		foreach ($_array as $_key => $_value)
		{
			if($selected == $_key)
			{
				$option .= '<option value="'.htmlspecialchars($_key).'" selected="selected">'.strip_tags(htmlspecialchars($_value)).'</option>';
			}
			else
			{
				$option .= '<option value="'.htmlspecialchars($_key).'">'.strip_tags(htmlspecialchars($_value)).'</option>';
			}
		}

		return $option;
	}
	public static function to_option($array,$selected = '')
	{
		$option = '';
		foreach ($array as $_key => $_value)
		{
			if($selected == $_key)
			{
				$option .= '<option value="'.htmlspecialchars($_key).'" selected="selected">'.strip_tags(htmlspecialchars($_value)).'</option>';
			}
			else
			{
				$option .= '<option value="'.htmlspecialchars($_key).'">'.strip_tags(htmlspecialchars($_value)).'</option>';
			}
		}

		return $option;
	}
	public static function show_car_info(&$data,$row)
	{
		$list_maker_code = \Api::get_list_maker();
		$makers = array('' => 'メーカーを選択して下さい');
		static $_GENRE_NAMES = array('1' => '----- 国産車 -----', '2' => '----- 輸入車 -----');
		foreach ($list_maker_code as $maker_value) {
			$genre_code = substr($maker_value['maker_code'], 0, 1);
			if ($genre_code > '2') { $genre_code = '2'; }
			$genre_name = $_GENRE_NAMES[$genre_code];
			if (isset($makers[$genre_name]) == false) {
				$makers[$genre_name] = array();
			}
			$makers[$genre_name][$maker_value['maker_code']] = $maker_value['maker'];
		}
		$data['car_maker_code'] = Fuel\Core\Form::select('car_maker_code', $row['car_maker_code'], $makers, array('class' => 'form-control'));

		if($row['car_maker_code'])
		{
			$list_model_code = \Api::get_list_model($row['car_maker_code']);
			$data['car_model_code'] = \Constants::array_to_option($list_model_code,'model_code','model',$row['car_model_code']);
		}

		if($row['car_maker_code'] && $row['car_model_code'])
		{
			$list_year = \Api::get_list_year_month($row['car_maker_code'],$row['car_model_code']);
			$data['car_year'] = \Constants::array_to_option($list_year,'year','year',$row['car_year']);
		}

		if($row['car_maker_code'] && $row['car_model_code'] && $row['car_year'])
		{
			$list_type_code = \Api::get_list_type_code($row['car_maker_code'],$row['car_model_code'],$row['car_year']);
			$data['car_type_code'] = \Constants::array_to_option($list_type_code,'type_code','type',$row['car_type_code']);
		}

		if($row['car_maker_code'] && $row['car_model_code'] && $row['car_year'] && $row['car_type_code'])
		{
			$list_grade_code = \Api::get_list_grade_code($row['car_maker_code'],$row['car_model_code'],$row['car_year'],$row['car_type_code']);
			$data['car_grade_code'] = \Constants::array_to_option($list_grade_code,'grade_code','grade',$row['car_grade_code']);
		}

		return $data;
	}

}
