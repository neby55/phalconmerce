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
	'shop' => array(
		'title' => '',
		'default_lang' => '',
		'default_currency' => '',
		'cookie_lang_name' => 'lang',
		'cookie_currency_name' => 'currency',
		'cookies_lifetime_in_days' => 365
	),
	'cacheDir' => APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
	'baseUri' => (isset($_SERVER['BASE_URI']) ? $_SERVER['BASE_URI'] : '').'/', // BASE_URI index generated thanks to public/.htaccess
	'imageFolder' => APP_PATH.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'files',
	'imageUri' => 'files',
	'image404Uri' => '',
	'adminDir' => 'admin',
	'adminTitle' => 'Phalconmerce',
	'adminTheme' => 'sb-admin2',
	'frontDir' => '',
	'frontTitle' => 'Phalconmerce.com',
	'frontTheme' => 'v1',
	'loadTranslationIndexex' => true,
	'apiEnabled' => true,
	'apiDir' => 'api',
	'apiCorsAllowOrigin' => array('localhost'), // set an array of allowed domains, or just the string *
	'cloudinary' => array(
		"cloud_name" => "",
		"api_key" => "",
		"api_secret" => "",
		'global_preset' => '',
		'global_folder' => '',
		'products_preset' => '',
		'products_folder' => '',
	)
));
