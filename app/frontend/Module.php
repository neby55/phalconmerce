<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Frontend;

use Phalcon\Di;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\View;
use Phalcon\Text;
use Phalconmerce\Services\CheckoutService;
use Phalconmerce\Services\EmailService;
use Phalconmerce\Services\FrontendService;
use Phalconmerce\Services\MyAccountService;
use Phalconmerce\Services\StockService;
use Phalconmerce\Services\TranslationService;

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
				'Frontend\Models' => __DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR,
				'Frontend\Controllers' => __DIR__ . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR,
				'Frontend\Controllers\Abstracts' => __DIR__ . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR,
			],
			true
		);
		// Adding composer
		$loader->registerFiles([
			APP_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'autoload.php'
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
		 * Trigger beforeRegisterServices
		 */
		$dependencyInjector->get('eventsManager')->fire('frontend:beforeRegisterServices', $dependencyInjector);

		/**
		 * The Logger component
		 */
		$dependencyInjector->setShared('logger', function () {
			$logger = new \Phalcon\Logger\Adapter\File(APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'frontend.log');
			return $logger;
		});

		/**
		 * Dispatcher
		 */
		$dependencyInjector->set('dispatcher', function () use ($dependencyInjector) {
			// Camelize actions
			$dependencyInjector->get('eventsManager')->attach(
				"dispatch:beforeDispatchLoop",
				function (Event $event, $dispatcher) {
					$dispatcher->setActionName(
						Text::camelize($dispatcher->getActionName())
					);
				}
			);

			$dispatcher = new Dispatcher();
			$dispatcher->setEventsManager($dependencyInjector->get('eventsManager'));
			$dispatcher->setDefaultNamespace("\\Frontend\\Controllers\\");
			return $dispatcher;
		});

		/**
		 * Sessions for frontend
		 */
		$dependencyInjector->set("session", function () {
			// All variables created will prefixed with "my-app-1"
			$session = new \Phalcon\Session\Adapter\Files(
				array(
					"uniqueId" => "frontend",
				)
			);
			$session->start();
			return $session;
		});

		/**
		 * Phalconmerce TranslationService
		 */
		$dependencyInjector->setShared('translation', function () {
			$service = new TranslationService();
			return $service;
		});

		/**
		 * Phalconmerce EmailService
		 */
		$dependencyInjector->setShared('email', function () {
			$service = new EmailService();
			return $service;
		});

		/**
		 * Phalconmerce FrontendService
		 */
		$dependencyInjector->setShared('frontendService', function () {
			$service = new FrontendService();
			return $service;
		});

		/**
		 * Phalconmerce CheckoutService
		 */
		$dependencyInjector->setShared('checkout', function () {
			$service = new CheckoutService();
			return $service;
		});

		/**
		 * Phalconmerce MyAccountService
		 */
		$dependencyInjector->setShared('myAccount', function () {
			$service = new MyAccountService();
			return $service;
		});

		/**
		 * Phalconmerce StockService
		 */
		$dependencyInjector->setShared('stockService', function () use ($dependencyInjector) {
			$service = new StockService();
			return $service;
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
			$view->setViewsDir(__DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . DI::getDefault()->get('config')->frontTheme . DIRECTORY_SEPARATOR);
			$view->setVar('langId', DI::getDefault()->get('translation')->getLangId());
			$view->setVar('lang',DI::getDefault()->get('translation')->getLangCode() );
			$view->setVar('front',DI::getDefault()->get('frontendService'));
			$view->setVar('translation',DI::getDefault()->get('translation'));
			return $view;
		});

		/**
		 * Trigger afterRegisterServices
		 */
		$dependencyInjector->get('eventsManager')->fire('frontend:afterRegisterServices', $dependencyInjector);
	}

}