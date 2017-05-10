<?php

use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalconmerce\Cli\Console as ConsoleApp;
use Phalcon\Loader;

// Define to specify directory call with CLI
define('CLI_PATH', dirname(__FILE__));

// Using the CLI factory default services container
$di = new CliDI();

// Load the configuration file (if any)
$configFile = __DIR__ . "/config/config.php";
if (is_readable($configFile)) {
	$config = include $configFile;
	$di->set("config", $config);
}

// Load the configuration file (if any)
$configFile = __DIR__ . "/config/phalconmerce.config.php";
if (is_readable($configFile)) {
	$configPhalconmerce = include $configFile;
	$di->set("configPhalconmerce", $configPhalconmerce);
}

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new Loader();

$loader->registerDirs(
	[
		__DIR__ . "/tasks",
		$config->application->modelsDir
	]
);
$loader->register();

// Create a console application and share it
$console = new ConsoleApp();
$console->setDI($di);
$di->setShared("console", $console);

/**
 * Prepare the console options processed by console's handle method
 */
$shortOpts = '';
$longOpts  = array(
	"table-prefix:",
	"all",
	"delete",
);

/**
 * Process the console arguments
 */
$arguments = [];
$counter = 0;
foreach ($argv as $arg) {
	if (substr($arg,0,1) != '-') {
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

try {
	// Handle incoming arguments
	$console->handle($arguments, $shortOpts, $longOpts);
} catch (\Phalcon\Exception $e) {
	echo $e->getMessage();

	exit(255);
}