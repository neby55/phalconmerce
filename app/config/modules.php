<?php
/**
 * Modules are globally registered in this file
 *
 * @var \Phalcon\Config $config
 * @var \Phalcon\Mvc\Application $application
 */

if (isset($application)) {
	$application->registerModules(
		array(
			'frontend' => [
				'className' => 'Frontend\Module',
				'path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'Module.php'
			],
			'backend' => [
				'className' => 'Backend\Module',
				'path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'Module.php'
			]
		)
	);
	$application->setDefaultModule('frontend');
}