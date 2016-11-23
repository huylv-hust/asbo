<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * define
 */
$_API_URL_USAPPY = 'https://usappy.com/api/';
$_API_URL_UPS = 'http://verify-ups.com/api/';
$_API_SECRET = 'sec';

return  array(
	'ss'     => array(
		'url_ss' => $_API_URL_USAPPY.'ss',
	),
	'car'    => array(
		'url_car' => $_API_URL_USAPPY.'car',
	),
	'member' => array(
		'url_card'          => $_API_URL_UPS.'getcardno',
		'url_member'        => $_API_URL_UPS.'getmemberbasic',
		'url_member_list'   => $_API_URL_UPS.'getmembers',
		'url_update_member' => $_API_URL_UPS.'updmemberbasic',
	),
	'secret' => $_API_SECRET,
);
