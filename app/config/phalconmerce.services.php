<?php

/** @var $di Phalcon\Di */
if (isset($di)) {
	/**
	 * Globally share the original config data to all components, if needed
	 */
	if (!$di->has('config')) {
		$di->set('config', function () use ($config) {
			return $config;
		}, true);
	}
	/**
	 * Globally share the config data to all components
	 */
	$di->set('configPhalconmerce', function () use ($configPhalconmerce) {
		return $configPhalconmerce;
	}, true);
}
else {
	die('DI should be instancied before phalconmerce services');
}