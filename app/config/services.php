<?php
/**
 * Services are globally registered in this file
 *
 * @var \Phalcon\Config $config
 */

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Group as RouterGroup;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The CONFIG component
 */
$di->setShared('config', $config);
$di->setShared('configPhalconmerce', $configPhalconmerce);

/**
 * The Logger component
 */
$di->set('logger', function () use ($config) {
	$logger = new \Phalcon\Logger\Adapter\File(APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'general.log');
	return $logger;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () use ($config) {
	$url = new UrlResolver();
	$url->setBaseUri($config->baseUri);

	return $url;
});

/**
 * Setting up the ROUTER component
 */
$router = new Router(false); // false to disable default Phalcon routing automatic routing

// Create a group with a common module and controller
$backendRouter = new RouterGroup(
	[
		"module" => "backend",
	]
);
// Add admin directory to all backend URL
$backendRouter->setPrefix(str_replace('//', '/', '/' . $config->adminDir));

// Include backend personnal routes
if (file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'routes.php')) {
	require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'routes.php';
}

// Mount RouterGroup on global router
$router->mount($backendRouter);

// If API enabled
if ($config->apiEnabled === true) {
	// Include API routes
	if (file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'routes.php')) {
		// Create a group with a common module and controller
		$apiRouter = new RouterGroup(
			[
				"module" => "api",
			]
		);
		// Add admin directory to all backend URL
		$apiRouter->setPrefix(str_replace('//', '/', '/' . $config->apiDir));

		require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'routes.php';

		// Mount RouterGroup on global router
		$router->mount($apiRouter);
	}
}

// Include frontend personnal routes
if (file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'routes.php')) {
	require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'routes.php';
}

$router->removeExtraSlashes(true);

// define default module
$router->setDefaultModule('frontend');

// default action
$router->setDefaultAction('index');

$di->set('router', $router);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () use ($config) {
	$dbConfig = $config->get('database')->toArray();
	$adapter = $dbConfig['adapter'];
	unset($dbConfig['adapter']);

	$class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

	return new $class($dbConfig);
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
	return new MetaDataAdapter();
});

/**
 * Register the direct flash and session flash services with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
	return new Phalcon\Flash\Direct(array(
		'error' => 'alert alert-danger',
		'success' => 'alert alert-success',
		'notice' => 'alert alert-info',
		'warning' => 'alert alert-warning'
	));
});
$di->set('flashSession', function () {
	return new Phalcon\Flash\Session(array(
		'error' => 'alert alert-danger',
		'success' => 'alert alert-success',
		'notice' => 'alert alert-info',
		'warning' => 'alert alert-warning'
	));
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
	$session = new SessionAdapter();
	$session->start();

	return $session;
});