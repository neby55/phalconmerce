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
		'Phalconmerce\Models\Checkout' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'checkout' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Checkout\Abstracts' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'checkout' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Exceptions' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'exceptions' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Exceptions\Abstracts' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'exceptions' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Generic' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'generic' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Generic\Abstracts' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'generic' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Popo' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'popo' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Popo\Abstracts' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'popo' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Popo\Generators' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'popo' . DIRECTORY_SEPARATOR . 'generators' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Popo\Generators\Popo' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'popo' . DIRECTORY_SEPARATOR . 'generators' . DIRECTORY_SEPARATOR . 'popo' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Popo\Generators\Db' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'popo' . DIRECTORY_SEPARATOR . 'generators' . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Models\Popo\Generators\Backend' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'popo' . DIRECTORY_SEPARATOR . 'generators' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Services' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Services\Abstracts' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Services\POMO' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR . 'pomo' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Plugins' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Plugins\Abstracts' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR,
		'Phalconmerce\Forms\Element' => PHALCONMERCE_PATH . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'element' . DIRECTORY_SEPARATOR,
	)
));