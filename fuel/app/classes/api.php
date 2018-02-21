<?php
/**
 * Api class
 * @author NamDD <namdd6566@seta-asia.com.vn>
 * @date 08/05/2015
 */

class Api
{
	public static $api = array();
	public static function _init()
	{
		static::$api = \Config::load('api');
	}

	/**
	 * get sscode
	 * @author NamDD
	 * @since 1.0.0
	 * @param
	 * @return array ss
	 */

	protected static function _api($url, $params, $method = 'post')
	{
		$api = $req = Fuel\Core\Request::forge($url, 'curl');
		$api->set_header('Content-Type', 'application/json');
		$api->set_method($method);
		$api->set_params($params);

		if (isset(static::$api['proxy']))
		{
			$api->set_option(CURLOPT_PROXY, static::$api['proxy']);
		}

		$res = $api->execute();
		if($api->response()->status == 200)
		{
				return json_decode($res,true);
		}

		Fuel\Core\Log::error('Api error: '.print_r($params,true).print_r($res,true));
		return[];
	}

	public static function get_ss_name($sscode = '')
	{

		if ($sscode === '')
			$url = static::$api['ss']['url_ss'];
		else
		{
			$url = static::$api['ss']['url_ss'].'?sscode='.$sscode;
		}

		try
		{
			$res = json_decode(\Fuel\Core\Cache::get('ss'.$sscode),true);
		}
		catch (\CacheNotFoundException $e)
		{
			$res = self::_api($url,array(),'get');
			if(count($res))
			{
				\Fuel\Core\Cache::set('ss'.$sscode, json_encode($res), 3600 * 24);
			}
		}
		return $res;
	}
	public static function search($branch_code ='',$keywork='')
	{
		$url = static::$api['ss']['url_ss'];
		$params = array(
			'branch_code' => $branch_code,
			'keyword'     => $keywork,
		);
		$res = self::_api($url, $params,'get');
		return $res;
	}

	/**
	 * get info card
	 * @param type $card_no
	 * @return array card_infor empty array error 500 else
	 */
	public static function get_info_card($card_no)
	{
		$url = static::$api['member']['url_card'];
		$params = array(
			'secret' => static::$api['secret'],
			'cardNo' => $card_no,
		);

		$res = self::_api($url, $params);
		if(count($res) == 0)
			return array('result' => 500);

		if(array_key_exists('member_kaiinCd',$res))
		{
			$res['result'] = 1;
			return $res;
		}

		return array('result' => 3);

	}
	/**
	 *
	 * @param type $kaiincd
	 * @param type $values
	 * @return array
	 */
	public static function update_member_basic($kaiincd,$values=array())
	{
		$url = static::$api['member']['url_update_member'];
		$values['secret']  = static::$api['secret'];
		$values['kaiinCd'] = $kaiincd;
		$res = self::_api($url, $values);
		if(count($res))
			return true;

		return false;
	}
	/**
	 *
	 */
	public static function get_member_base_info($member_id)
	{
		return Api::get_member_info($member_id,'result,member_telNo2,member_telNo1,member_kaiinName,member_kaiinKana,member_mailAddress1,member_mailAddress2');
	}
	public static function get_member_info($member_id, $list_title='', $mail_addr = '', $mob_id='')
	{
		if($member_id == '' && $mail_addr == '' && $mob_id == '')
		{
			return -1;
		}

		$values = array();
		$url = static::$api['member']['url_member'];
		$values['secret']   = static::$api['secret'];
		if($member_id != '')
		{
			$values['kaiinCd']  = $member_id;
		}

		if($mail_addr != '')
		{
			$values['mailAddr']  = $mail_addr;
		}

		if($mob_id != '')
		{
			$values['mobId']  = $mob_id;
		}

		if($list_title != '')
		{
			$list_title = $list_title.',member_kaiinCd';
			$values['columns']  = $list_title;
		}

		$res = self::_api($url, $values);

		if(count($res) == 0)
			return -1;

		if(array_key_exists('member_kaiinCd',$res))
		{
			return $res;
		}

		return -1;
	}


	public static function get_list_maker()
	{
		try
		{
			$res = json_decode(\Fuel\Core\Cache::get('list_maker'),true);
		}
		catch (\CacheNotFoundException $e)
		{
			$url = static::$api['car']['url_car'];
			$res = self::_api($url,array(),'get');
			if(count($res))
			{
				\Fuel\Core\Cache::set('list_maker', json_encode($res), 3600 * 24);
			}
			else
			{
				$res = false;
			}
		}

		return $res;
	}

	public static function get_list_model($maker_code)
	{
		try
		{
			$res = json_decode(\Fuel\Core\Cache::get('maker_code'.$maker_code),true);
		}
		catch (\CacheNotFoundException $e)
		{
			$res = self::get_car_info($maker_code);
			if(count($res))
			{
				\Fuel\Core\Cache::set('maker_code'.$maker_code, json_encode($res), 3600 * 24);
			}
			else
			{
				$res = false;
			}
		}
		return $res;
	}

	public static function get_list_year_month($maker_code,$model_code)
	{
		return self::get_car_info($maker_code,$model_code);
	}

	public static function get_list_type_code($maker_code,$model_code,$year)
	{
		return self::get_car_info($maker_code,$model_code,$year);
	}

	public static function get_list_grade_code($maker_code,$model_code,$year,$type_code)
	{
		return self::get_car_info($maker_code,$model_code,$year,$type_code);
	}


	private static function get_car_info($maker_code = '',$model_code = '',$year = '',$type_code = '')
	{
		$url = static::$api['car']['url_car'];
		$values = array();
		if($maker_code != '')
		{
			$values['maker_code'] = $maker_code;
		}

		if($model_code != '')
		{
			$values['model_code'] = $model_code;
		}

		if($year != '')
		{
			$values['year'] = $year;
		}

		if($type_code != '')
		{
			$values['type_code'] = $type_code;
		}

		$res = self::_api($url, $values,'get');

		if(count($res) == 0)
			return array('result' => 500);

		return $res;
	}
	public static function get_members($member_id,$list_title='members_kaiinKana')
	{
		$url = static::$api['member']['url_member_list'];
		$values['secret']  = static::$api['secret'];
		$values['kaiinCd'] = $member_id;
		$values['columns'] = $list_title.',members_kaiinCd';
		$res = self::_api($url, $values);

		if(count($res) == 0)
			return -1;

		$member = array();
		if(array_key_exists('members_kaiinCd',$res))
		{
			$members_kaiincd = $res['members_kaiinCd'];
			for($i = 0; $i < count($members_kaiincd); ++$i)
			{
				$member[$members_kaiincd[$i]] = $res['members_kaiinKana'][$i];
			}

			return $member;
		}

		return -1;
	}

	public static function search_ss($params, $only_opened = true)
	{
		array_filter($params);
		if ($only_opened)
		{
			$params['only_opened'] = 1;
		}

		try
		{
			$res = json_decode(\Fuel\Core\Cache::get(md5(json_encode($params))),true);
		}
		catch (\CacheNotFoundException $e)
		{
			$url = static::$api['ss']['url_ss'] ;
			$res = self::_api($url,$params,'get');
			if(count($res))
			{
				Fuel\Core\Cache::set(md5(json_encode($params)), json_encode($res), 3600 * 24);
			}
		}

		return $res;

	}

}
