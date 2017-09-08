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

class MenuControllerIndexLink extends MenuLink {
	public function __construct($controller) {
		$this->link = Di::getDefault()->get('url')->get(array(
			'for' => 'backend-controller',
			'controller' => $controller
		));
	}
}