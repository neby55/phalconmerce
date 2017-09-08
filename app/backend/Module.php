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
use Phalcon\Di;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\View;
use Phalcon\Events\Manager as EventsManager;
use Phalconmerce\Plugins\NotFoundPlugin;
use Phalconmerce\Plugins\BackendSecurityPlugin;
use Phalconmerce\Services\BackendService;

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
				'Backend\Models' => __DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR,
				'Backend\Controllers' => __DIR__ . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR,
				'Backend\Forms' => __DIR__ . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR,
				'Phalconmerce\Services' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'phalconmerce' . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR,
				'Phalconmerce\Services\Abstracts' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'phalconmerce' . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR,
				'Phalconmerce\Plugins' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'phalconmerce' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR,
				'Phalconmerce\Plugins\Abstracts' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'phalconmerce' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR,
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
		 * The Logger component
		 */
		$dependencyInjector->set('logger', function () {
			$logger = new \Phalcon\Logger\Adapter\File(APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'backend.log');
			return $logger;
		});

		// TODO handle ACL in for backend, like invo/plugins/SecurityPlugin

		/**
		 * Dispatcher
		 */
		$dependencyInjector->set('dispatcher', function () {
			$eventsManager = new EventsManager;
			/**
			 * Check if the user is allowed to access certain action using the SecurityPlugin
			 */
			$eventsManager->attach('dispatch:beforeDispatch', new BackendSecurityPlugin);
			/**
			 * Handle exceptions and not-found exceptions using NotFoundPlugin
			 */
			$eventsManager->attach('dispatch:beforeException', new NotFoundPlugin);

			/**
			 * Create the dispatcher
			 */
			$dispatcher = new Dispatcher();
			$dispatcher->setDefaultNamespace("\\Backend\\Controllers\\");
			$dispatcher->setEventsManager($eventsManager);

			return $dispatcher;
		});

		/**
		 * Phalconmerce backendService
		 */
		$dependencyInjector->set('backendService', function () {
			$service = new BackendService();
			return $service;
		});

		/**
		 * Setting up the VIEW component
		 */
		$dependencyInjector->set('view', function () {
			$view = new View();
			$view->setViewsDir(__DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . DI::getDefault()->get('config')->adminTheme . DIRECTORY_SEPARATOR);
			return $view;
		});
	}

}