<?php

use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Loader;

// Define to specify directory call with CLI
define('CLI_PATH', dirname(__FILE__));

$debug = new \Phalcon\Debug();
$debug->listen();

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

		// register the installed modules
		$this->registerModules(array(
			'cli' => [
				'className' => 'Cli\Module',
				'path' => __DIR__ . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'Module.php'
			],
			'phalconmerce' => [
				'className' => 'Phalconmerce\Module',
				'path' => __DIR__ . DIRECTORY_SEPARATOR . 'phalconmerce' . DIRECTORY_SEPARATOR . 'Module.php'
			]
		));

		$loader->register();
	}

	public function main() {
		$di = new CliDI();

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