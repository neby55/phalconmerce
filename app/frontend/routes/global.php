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
 * Tip : settings a name to each route is very helpful
 * @var \Phalcon\Mvc\Router $router
 */

/*$router->add('/contact',
	[
		"controller" => "index",
		"action"     => "contact",
	]
)->setName('contact');*/

/**
 * If routes are diferents depending current language
 */
/*$currentLang = \Phalconmerce\Services\TranslationService::getCurrentLang();

if (!empty($currentLang)) {
	require __DIR__.DIRECTORY_SEPARATOR.$currentLang.'.php';
}*/