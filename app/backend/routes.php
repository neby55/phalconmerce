<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

/**
 * You can add URL using $backendRouter
 * @var \Phalcon\Mvc\Router\Group $backendRouter
 */
$backendRouter->add('/',
	[
		"controller" => "index",
		"action"     => "index",
	]
)->setName('backend-index');
$backendRouter->add('/login',
	[
		"controller" => "login",
		"action"     => "index",
	]
)->setName('backend-login');

$backendRouter->add(
	'/:controller',
	[
		'controller' => 1,
		'action' => 'index'
	]
);

$backendRouter->add(
	'/:controller/:action/:params',
	[
		"controller" => 1,
		"action"     => 2,
		"params"     => 3,
	]
);