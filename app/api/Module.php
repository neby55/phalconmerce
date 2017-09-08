<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Api;

use Phalcon\Mvc\Dispatcher;
use Phalcon\Di;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\View;
use Phalcon\Events\Manager as EventsManager;

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
				// TODO delete namespaces if useless
				'Api\Models' => __DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR,
				'Api\Controllers' => __DIR__ . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR,
				'Api\Controllers\Abstracts' => __DIR__ . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR,
				'Backend\Forms' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR,
			],
			true
		);
		$loader->register();
	}

	/**
	 * Registers services related to the module
	 *
	 * @param mixed $dependencyInjector
	 */
	public function registerServices(DiInterface $dependencyInjector) {
		/**
		 * Dispatcher
		 */
		$dependencyInjector->set('dispatcher', function () {
			$dispatcher = new Dispatcher();
			$dispatcher->setDefaultNamespace("\\Api\\Controllers\\");
			return $dispatcher;
		});


		/**
		 * Setting up the VIEW component => empty because useless
		 */
		$dependencyInjector->set('view', function () {
			return new View();
		}, true);
	}

}