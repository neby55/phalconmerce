<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

/**
 * Never modify this file, you should edit files in "routes" folder
 */

$router->add('/',
	[
		"controller" => "index",
		"action"     => "index",
	]
)->setName('home');

$router->add('/sitemap.xml',
	[
		"controller" => "sitemap",
		"action"     => "index",
	]
)->setName('sitemap');

$router->add('/checkout',
	[
		"controller" => "checkout",
		"action"     => "index",
	]
)->setName('checkout_index');

$router->add('/newsletter-confirm/([0-9a-z]{32})/',
	[
		"controller" => "index",
		"action"     => "newsletterEmailConfirm",
		"token" => 1
	]
)->setName('newsletter-email-confirm');

$router->add('/friend-sponsoring-confirm/([0-9a-z]{32})/',
	[
		"controller" => "index",
		"action"     => "friendSponsoringConfirm",
		"token" => 1
	]
)->setName('friend-sponsoring-confirm');

// Include developer defined routes
require __DIR__.DIRECTORY_SEPARATOR.'routes'.DIRECTORY_SEPARATOR.'global.php';

$router->notFound(
	[
		"controller" => "errors",
		"action"     => "show404",
	]
);

// Retrieve DB defined permalinks
$dbUrlList = \Phalconmerce\Models\Utils::loadData('routes');
if (is_array($dbUrlList)) {
	/**
	 * @var \Phalconmerce\Models\Popo\Url $currentUrlObject
	 */
	foreach ($dbUrlList as $currentPermalink=>$currentUrlObject) {
		if (substr($currentPermalink,0,1) != '/') {
			$currentPermalink = '/'.$currentPermalink;
		}
		$router->add($currentPermalink,
			[
				"controller" => "url",
				"action"     => "dispatcher",
				"url" => $currentUrlObject
				//"params" => array($currentUrlObject)
			]
		);
	}
}

// Gets every checkout routes
$checkoutRoutes = \Phalconmerce\Models\Utils::loadData('checkoutRoutes');
$router = \Phalconmerce\Models\Generic\Route::addRoutesToRouter($checkoutRoutes, $router);

// Gets every payments routes
$paymentRoutes = \Phalconmerce\Models\Utils::loadData('paymentRoutes');
$router = \Phalconmerce\Models\Generic\Route::addRoutesToRouter($paymentRoutes, $router);

// Gets every myAccount routes
$myAccountRoutes = \Phalconmerce\Models\Utils::loadData('myAccountRoutes');
$router = \Phalconmerce\Models\Generic\Route::addRoutesToRouter($myAccountRoutes, $router);