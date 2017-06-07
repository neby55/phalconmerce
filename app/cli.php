<?php

use Phalcon\Cli\Router;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Loader;

// Define to specify directory call with CLI
define('CLI_PATH', dirname(__FILE__));

// TODO maybe improve CLI with docopt

class Console extends \Phalcon\CLI\Console {
	public static $shortOpts = '';

	public static $longOpts = array(
		"table-prefix:",
		"all",
		"delete",
	);

	public function __construct() {
		$loader = new \Phalcon\Loader();
		$loader->registerNamespaces(
			array(
				'Cli\Models' => __DIR__ . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'models',
				'Cli\Tasks' => __DIR__ . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'tasks'
			)
		);
		$loader->register();
	}

	public function main() {
		$di = new CliDI();

		// registering config
		$di->set('config', function () {
			return include __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
		});

		// registering phalconmerce config
		$di->set('configPhalconmerce', function () {
			return include __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'phalconmerce.config.php';
		});

		// registering a router
		$di->set('router', function () {
			$router = new \Phalcon\CLI\Router();

			return $router;
		});

		// registering a dispatcher
		$di->set('dispatcher', function () use ($di) {

			// obtain the standard eventsManager from the DI
			$eventsManager = $di->getShared('eventsManager');

			$eventsManager->attach("dispatch:beforeDispatchLoop", function ($event, $dispatcher) {
				$dispatcher->setActionName(\Phalcon\Text::camelize($dispatcher->getActionName()));
			});

			$dispatcher = new \Phalcon\CLI\Dispatcher();
			// bind the EventsManager to the Dispatcher
			$dispatcher->setEventsManager($eventsManager);

			$dispatcher->setDefaultNamespace('Cli\Tasks');
			$dispatcher->setNamespaceName('Cli\Tasks');

			return $dispatcher;
		});

		// register URL to avoid fatal error
		$di->set("url", function() {
			$url = new \Phalcon\Mvc\Url();
			$url->setBaseUri("/");
			return $url;
		});

		// registering router (mandatory for namespaces and modules)
		$di->set("router", function () {
			$router = new Router(true);
			return $router;
		});

		// Ading phalconmerce namespaces
		$loader = new \Phalcon\Loader();
		$loader->registerNamespaces(
			$di->get('configPhalconmerce')->get('namespaces')->toArray(),
			true
		);
		$loader->register();

		// registering router (mandatory for namespaces and modules)
		$di->set("console", $this);

		$this->setDI($di);
	}

	/**
	 * Handle the whole command-line tasks
	 *
	 * @param array $arguments
	 * @param string $shortOpts
	 * @param array $longOpts
	 */
	public function handle($arguments = array(), $shortOpts = '', $longOpts = array()) {
		global $argv;

		/**
		 * Process the console arguments
		 */
		$arguments = [];
		$counter = 0;
		foreach ($argv as $arg) {
			if (substr($arg, 0, 1) != '-') {
				if ($counter === 1) {
					$arguments["task"] = $arg;
				}
				elseif ($counter === 2) {
					$arguments["action"] = $arg;
				}
				elseif ($counter >= 3) {
					$arguments["params"][] = $arg;
				}
				$counter++;
			}
		}
		$this->_options = getopt(self::$shortOpts, self::$longOpts);

		parent::handle($arguments);
	}

	/**
	 * @return mixed
	 */
	public function getOptions() {
		return $this->_options;
	}
}

$console = new Console();
$console->main();

try {
	// Handle incoming arguments
	$console->handle();
} catch (\Phalcon\Exception $e) {
	echo $e->getMessage();

	exit(255);
}