<?php

if (defined('APP_PATH')) {
	try {
		/**
		 * Read the configuration
		 */
		$configPhalconmerce = include APP_PATH . "/app/config/phalconmerce.config.php";

		/**
		 * Read auto-loader
		 */
		include APP_PATH . "/app/config/phalconmerce.loader.php";

		/**
		 * Read services
		 */
		include APP_PATH . "/app/config/phalconmerce.services.php";

		/**
		 * Setup modules
		 */
		include APP_PATH . "/app/config/phalconmerce.modules.php";

	}
	catch (\Exception $e) {
		echo $e->getMessage() . '<br>';
		echo '<pre>' . $e->getTraceAsString() . '</pre>';
	}
}
else {
	die('Phalconmerce index called directly :(');
}