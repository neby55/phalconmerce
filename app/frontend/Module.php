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
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\View;
use Phalconmerce\Services\FrontendService;
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
				'POMO' => APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'phalconmerce' . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR . 'pomo' . DIRECTORY_SEPARATOR,
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
			$logger = new \Phalcon\Logger\Adapter\File(APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'frontend.log');
			return $logger;
		});

		/**
		 * Dispatcher
		 */
		$dependencyInjector->set('dispatcher', function () {
			$dispatcher = new Dispatcher();
			$dispatcher->setDefaultNamespace("\\Frontend\\Controllers\\");
			return $dispatcher;
		});

		/**
		 * Phalconmerce TranslationService
		 */
		$dependencyInjector->set('translation', function () {
			$service = new TranslationService();
			return $service;
		});

		/**
		 * Phalconmerce FrontendService
		 */
		$dependencyInjector->set('frontendService', function () {
			$service = new FrontendService();
			return $service;
		});

		/**
		 * Setting up the VIEW component
		 */
		$dependencyInjector->set('view', function () {
			$view = new View();
			$view->setViewsDir(__DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . DI::getDefault()->get('config')->frontTheme . DIRECTORY_SEPARATOR);
			$view->setVar('langId', DI::getDefault()->get('translation')->getLangId());
			$view->setVar('lang',DI::getDefault()->get('translation')->getLangCode() );
			return $view;
		});
	}

}