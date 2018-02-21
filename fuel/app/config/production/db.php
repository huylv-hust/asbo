<?php
/**
 * Use this file to override global defaults.
 *
 * See the individual environment DB configs for specific config information.
 */

return array(
	'default' => array(
		'connection'  => array(
			'dsn'        => 'mysql:host=asdb01;dbname=usappyas',
			'username'   => 'www',
			'password'   => 'Saetb3MQas',
		),
	),
	'oracle' => array(
		'type' => 'pdo',
		'connection' => array(
			'dsn'        => 'oci:dbname=//aspdb01:1521/USAMIUP',
			'username'   => 'asweb',
			'password'   => 'asweb_pass_00',
			'persistent' => false
		),
		'identifier' => '',
		'table_prefix' => '',
		'charset' => 'utf8',
		'caching' => true,
		'profiling' => false,
	),
	'redis' => array(
		'default' => array(
			'hostname' => 'asdb01',
			'port'     => 6379
		)
	),
);
