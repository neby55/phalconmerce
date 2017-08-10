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
			]
		);
	}
}