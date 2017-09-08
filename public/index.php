<?php

error_reporting(E_ALL);

define('APP_PATH', realpath('..'));

$debug = new \Phalcon\Debug();
$debug->listen();

try {

	/**
	 * Read the configuration
	 */
	$config = include APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

	/**
	 * Read the Phalconmerce configuration
	 */
	$configPhalconmerce = include APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'phalconmerce.config.php';

	/**
	 * Read auto-loader
	 */
	include APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'loader.php';

	/**
	 * Read services
	 */
	include APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.php';

	/**
	 * Handle the request
	 */
	$application = new \Phalcon\Mvc\Application($di);

	/**
	 * Load modules
	 */
	include APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'modules.php';

	echo $application->handle()->getContent();

}
catch (\Exception $e) {
	// TODO use log system
	echo $e->getMessage() . '<br>';
	echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
