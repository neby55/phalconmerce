<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend;

use Phalcon\Mvc\Dispatcher;
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
				'Backend\Models' => __DIR__ . DIRECTORY_SEPARATOR. 'models' . DIRECTORY_SEPARATOR,
				'Backend\Controllers' => __DIR__ . DIRECTORY_SEPARATOR .'controllers' . DIRECTORY_SEPARATOR,
				'Backend\Forms' => __DIR__ . DIRECTORY_SEPARATOR .'forms' . DIRECTORY_SEPARATOR,
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
		// TODO handle ACL in for backend, like invo/plugins/SecurityPlugin

		/**
		 * URL
		 */
		$dependencyInjector->get('url')->setBaseUri($dependencyInjector->get('config')->baseUri.'/'.$dependencyInjector->get('config')->adminDir.'/');

		/**
		 * Dispatcher
		 */
		$dependencyInjector->set('dispatcher', function ()
		{
			$dispatcher = new Dispatcher();
			$dispatcher->setDefaultNamespace("\\Backend\\Controllers\\");
			return $dispatcher;
		});

		/**
		 * Setting up the VIEW component
		 */
		$dependencyInjector->set('view', function () {
			$view = new View();
			$view->setViewsDir(__DIR__ . '/views/sb-admin2/');
			return $view;
		});
	}

}