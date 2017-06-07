<?php

if (!defined('PHALCONMERCE_PATH')) {
	define('PHALCONMERCE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'phalconmerce');
}

return new \Phalcon\Config(array(
	'modelsDir' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models',
	'popoModelsDir' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'popo',
	'cacheDir' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'phalconmerce',
	'namespaces' => array(
		'Phalconmerce\Models' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Popo' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'popo' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Popo\Abstracts' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'popo' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Popo\Popogenerator' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'popo' . DIRECTORY_SEPARATOR . 'popogenerator' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Popo\Tablegenerator' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'popo' . DIRECTORY_SEPARATOR . 'tablegenerator' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Services' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR,
	)
));