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
	/** @var string */
	private $controller;

	public function __construct($controller) {
		parent::__construct();
		$this->controller = $controller;
		$this->link = Di::getDefault()->get('url')->get(array(
			'for' => 'backend-controller-index',
			'controller' => $this->controller
		));
	}

	/**
	 * @return bool
	 */
	public function isActive() {
		if (is_object(self::$router)) {
			return self::$router->getControllerName() == $this->controller;
		}
		return false;
	}
}