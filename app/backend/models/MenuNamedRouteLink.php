<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Models;

use Phalcon\Di;

class MenuNamedRouteLink extends MenuLink {
	public function __construct($routeName, $params=array()) {
		if (is_array($params) && sizeof($params) > 0) {
			$params['for'] = $routeName;
		}
		else {
			$params = array(
				'for' => $routeName
			);
		}
		$this->link = Di::getDefault()->get('url')->get($params);
	}
}