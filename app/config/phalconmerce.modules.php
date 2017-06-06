<?php

/**
 * Register application modules
 */

if (isset($application)) {
	$application->registerModules(
		[
			'frontend' => [
				'className' => 'Modules\Modules\Frontend\Module',
				'path' => __DIR__ . '/../modules/frontend/Module.php'
			],
			'backend' => [
				'className' => 'Modules\Modules\Dashboard\Module',
				'path' => __DIR__ . '/../modules/dashboard/Module.php'
			]
		]
	);
}