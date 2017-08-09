<?php

defined('APP_PATH') || define('APP_PATH', realpath('.'));

return new \Phalcon\Config(array(
	'database' => array(
		'adapter' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'dbname' => '',
		'charset' => 'utf8',
	),
	'cacheDir' => APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
	'baseUri' => isset($_SERVER['BASE_URI']) ? $_SERVER['BASE_URI'] : '', // BASE_URI index generated thanks to public/.htaccess
	'adminDir' => 'admin',
	'adminTitle' => 'Phalconmerce',
	'adminTheme' => 'sb-admin2',
	'apiEnabled' => true,
	'apiDir' => 'api',
));
