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
 * Remember, first route=less priority, last route=most priority
 * @var \Phalcon\Mvc\Router\Group $backendRouter
 */

$backendRouter->add(
	'/:controller/:action/:params',
	[
		"controller" => 1,
		"action"     => 2,
		"params"     => 3,
	]
);

$backendRouter->add(
	'/:controller',
	[
		'controller' => 1,
		'action' => 'index'
	]
)->setName('backend-controller-index');
$backendRouter->add(
	'/:controller/new/',
	[
		'controller' => 1,
		'action' => 'new'
	]
)->setName('backend-controller-new');
$backendRouter->add(
	'/:controller/edit/:id',
	[
		'controller' => 1,
		'action' => 'edit',
		'id'     => 2,
	]
)->setName('backend-controller-edit');
$backendRouter->add(
	'/:controller/save/',
	[
		'controller' => 1,
		'action' => 'save'
	]
)->setName('backend-controller-save');
$backendRouter->add(
	'/:controller/delete/:id',
	[
		'controller' => 1,
		'action' => 'delete',
		'id'     => 2,
	]
)->setName('backend-controller-delete');

$backendRouter->add('/',
	[
		'controller' => 'index',
		'action'     => 'index',
	]
)->setName('backend-index');
$backendRouter->add('/login',
	[
		'controller' => 'login',
		'action'     => 'index',
	]
)->setName('backend-login');
$backendRouter->add('/logout',
	[
		'controller' => 'login',
		'action'     => 'logout',
	]
)->setName('backend-logout');