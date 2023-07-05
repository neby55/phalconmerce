<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend;

use Phalcon\Filter;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Di;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\View;
use Phalcon\Events\Manager as EventsManager;
use Phalconmerce\Plugins\NotFoundPlugin;
use Phalconmerce\Plugins\BackendSecurityPlugin;
use Phalconmerce\Services\StockService;
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
				'Backend\Controllers\Abstracts' => __DIR__ . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR,
				'Backend\Forms' => __DIR__ . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR,
			],
			true
		);
		// Adding composer
		$loader->registerFiles([
			APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'autoload.php'
		]);
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
		$dependencyInjector->setShared('logger', function () {
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
		 * Sessions for backend
		 */
		$dependencyInjector->set("session", function () {
			$session = new \Phalcon\Session\Adapter\Files(
				array(
					"uniqueId" => "backend",
				)
			);
			$session->start();
			return $session;
		});

		/**
		 * Phalconmerce backendService
		 */
		$dependencyInjector->setShared('backendService', function () {
			$service = new BackendService();
			return $service;
		});

		/**
		 * Phalconmerce stockService
		 */
		$dependencyInjector->setShared('stockService', function () {
			$service = new StockService();
			return $service;
		});

		/**
		 * Filter service and add a HTML filter
		 */
		$dependencyInjector->setShared('filter', function () {
			$filter = new Filter();
			$filter->add('html', function ($value) {
				return $value;
			});
			return $filter;

		});

		/**
		 * Cloudinary API
		 */
		if (class_exists('\Cloudinary')) {
			\Cloudinary::config($dependencyInjector->get('config')->cloudinary->toArray());
			$dependencyInjector->setShared('cloudinary', function () {
				$api = new \Cloudinary\Api();
				return $api;
			});
		}

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