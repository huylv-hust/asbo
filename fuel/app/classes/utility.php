<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Utility {
	/*
	 * Time to string integer
	 *
	 * @since 26/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */

	public static function time_to_string($hours, $minutes) {
		return ($hours * 60) + $minutes;
	}

	/*
	 * String integer to hours
	 *
	 * @since 26/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 * @return h = hours, m = minutes
	 */

	public static function string_to_time($time_string, $get_minutes = false) {
		$time = $time_string / 60;
		$minutes = $time_string % 60;

		$result = (int) $time;
		if ($get_minutes) {
			$result = $minutes;
		}

		return $result;
	}

	/*
	 * Debug data
	 *
	 * @since 08/05/2015
	 * @author Ha Huu Don <donhh6551@seta-asia.com.vn>
	 */

	public static function debug($value, $die = true) {
		echo '<pre>';
		print_r($value);
		echo '</pre>';
		if ($die) {
			die();
		}
	}

	private static function _bit2mask($bit) {
		$bit = intval($bit);
		return bindec(
				str_repeat('1', $bit) . str_repeat('0', 32 - $bit)
		);
	}

	/*
	 * check IP range.
	 *
	 * @since 08/26/2015
	 * @author Y.Hasegawa <hasegawa@d-o-m.jp>
	 */

	public static function is_include_ip($ip, $ranges) {
		if (is_array($ranges) == false) {
			$ranges = array($ranges);
		}

		$ip_long = ip2long($ip);

		foreach ($ranges as $range) {
			@list($range_ip, $bit) = explode('/', $range);
			if (strlen($bit)) {
				$range_ip_long = ip2long($range_ip);
				$mask = self::_bit2mask($bit);
				if (($ip_long & $mask) == ($range_ip_long & $mask)) {
					return true;
				}
			} else if ($ip == $range) {
				return true;
			}
		}

		return false;
	}

	public static function make_csvline($columns) {
		foreach ($columns as $k => $v) {
			$columns[$k] = str_replace('"', '""', $v);
		}
		$line = '"' . implode('","', $columns) . '"';
		return mb_convert_encoding($line, 'SJIS-win', 'UTF-8') . "\r\n";
	}

	public static function sendmail($mailto, $subject, $data, $template = false) {
		$email = \Email::forge();
		$email_config = Config::get('email');
		$email->from($email_config['from'], $email_config['name']);
		$email->to($mailto);
		$email->subject($subject);

		if ($template) {
			$email->body(\View::forge($template, $data));
		}

		try {
			$email->send();
			return true;
		} catch (\EmailValidationFailedException $e) {
			Fuel\Core\Log::error('Mail validation: ' . json_encode($mailto));
		} catch (\EmailSendingFailedException $e) {
			Fuel\Core\Log::error('Mail send failed: ' . json_encode($mailto));
		}
	}

	public static function convert_customer_info_oracel($customer) {
		$info = array();
		$customer = current($customer);
		if ($customer) {
			$info['member_kaiinName'] = $customer['customer_name'];
			$info['member_kaiinKana'] = $customer['customer_kana'];
			$info['member_telNo1'] = $customer['mobile_tel'];
			$info['member_telNo2'] = $customer['house_tel'];
			$info['member_mailAddress1'] = $customer['email_mobile'];
			$info['member_mailAddress2'] = $customer['email_pc'];
		}

		return $info;
	}

}
