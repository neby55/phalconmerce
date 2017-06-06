<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce;

use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\DiInterface;

class Module implements ModuleDefinitionInterface {
	/**
	 * Registers an autoloader related to the module
	 *
	 * @param mixed $dependencyInjector
	 */
	public function registerAutoloaders(DiInterface $dependencyInjector = null) {
		$loader = new Loader();
		$loader->registerNamespaces(
			[
				'Phalconmerce\Models' => __DIR__ . "/models/",
				'Phalconmerce\Models\Cli' => __DIR__ . "/models/cli/",
				'Phalconmerce\Controllers' => __DIR__ . "/controllers/"
			]
		);
		$loader->register();
	}

	/**
	 * Registers services related to the module
	 *
	 * @param mixed $dependencyInjector
	 */
	public function registerServices(DiInterface $dependencyInjector) {
		// Load the configuration file (if any)
		$configFile = dirname(dirname(__FILE__)) . "/config/config.php";
		if (is_readable($configFile)) {
			$config = include $configFile;
			$dependencyInjector->set("config", $config);
		}
	}

}