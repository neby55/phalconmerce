<?php
/**
 * Modules are globally registered in this file
 *
 * @var \Phalcon\Config $config
 * @var \Phalcon\Mvc\Application $application
 */

if (isset($application)) {
	$modulesList = array(
		'frontend' => [
			'className' => 'Frontend\Module',
			'path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'Module.php'
		],
		'backend' => [
			'className' => 'Backend\Module',
			'path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'Module.php'
		]
	);
	// if API enabled
	if (isset($apiRouter)) {
		$modulesList['api'] = [
			'className' => 'Api\Module',
			'path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'Module.php'
		];
	}
	$application->registerModules($modulesList);
	$application->setDefaultModule('frontend');
}