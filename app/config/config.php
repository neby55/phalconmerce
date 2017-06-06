<?php

defined('APP_PATH') || define('APP_PATH', realpath('.'));

return new \Phalcon\Config(array(
	'database' => array(
		'adapter' => 'Mysql',
		'host' => 'localhost',
		'username' => 'pgw-plugin-store',
		'password' => 'pgw-plugin-store',
		'dbname' => 'pgw-plugin-store',
		'charset' => 'utf8',
	),
	'cacheDir' => APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
	'baseUri' => '/pgw/phalconmerce/',
));
