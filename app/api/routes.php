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
		'action' => 'list'
	]
);
// Create
$apiRouter->addPost(
	'/{entity}',
	[
		'controller' => 'default',
		'action' => 'create'
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
$apiRouter->addPut(
	'/{entity}/{id:[0-9]+}',
	[
		"controller" => 'default',
		"action"     => 'replace'
	]
);
$apiRouter->addPatch(
	'/{entity}/{id:[0-9]+}',
	[
		"controller" => 'default',
		"action"     => 'modify'
	]
);
$apiRouter->addDelete(
	'/{entity}/{id:[0-9]+}',
	[
		"controller" => 'default',
		"action"     => 'delete'
	]
);