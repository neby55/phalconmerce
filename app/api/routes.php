<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

/**
 * You can add URL using $apiRouter
 * @var \Phalcon\Mvc\Router\Group $apiRouter
 */

/**
 * COLLECTIONS
 */
// Read List
$apiRouter->addGet(
	'/{entity}',
	[
		'controller' => 'default',
		'action' => 'list',
		"params"     => 1,
	]
);
// Create
$apiRouter->addPost(
	'/{:entity}',
	[
		'controller' => 'default',
		'action' => 'create',
		"params"     => 1,
	]
);

/**
 * ITEMS / ENTITIES
 */
$apiRouter->addGet(
	'/{entity}/{id:[0-9]+}',
	[
		"controller" => 'default',
		"action"     => 'read'
	]
);