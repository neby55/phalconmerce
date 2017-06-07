<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

/**
 * You can add URL using $router
 * @var \Phalcon\Mvc\Router $router
 */

$router->add('/',
	[
		"controller" => "index",
		"action"     => "index",
	]
);
$router->notFound(
	[
		"controller" => "index",
		"action"     => "route404",
	]
);