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
		'contact_recipient' => '',
		'default_lang' => '',
		'default_currency' => '',
		'default_country' => 0,
		'cookie_lang_name' => 'lang',
		'cookie_currency_name' => 'currency',
		'cookies_lifetime_in_days' => 365,
		'date_format' => 'd/m/Y'
	),
	'mailer' => array(
		'sender' => 'no-reply@bernard-orcel.com',
		'protocol' => 'smtp',
		'host' => 'smtp.googlemail.com',
		'port' => 587,
		'username' => 'alerte.pgw@gmail.com',
		'password' => 'wks_c_04',
		'smtp_secure' => 'tls',
		'charset' => 'UTF-8',
		'debugLevel' => 0,
		'options' => array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		)
	),
	'devMode' => true,
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
	'loadTranslationIndexes' => true,
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
