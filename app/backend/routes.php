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
);