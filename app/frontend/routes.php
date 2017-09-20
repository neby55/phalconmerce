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

// Include developer defined routes
require __DIR__.DIRECTORY_SEPARATOR.'routes'.DIRECTORY_SEPARATOR.'global.php';

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
};