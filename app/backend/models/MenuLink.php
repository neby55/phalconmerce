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

abstract class MenuLink {
	/** @var string */
	protected $link;

	/** @var \Phalcon\Mvc\Router */
	protected static $router;

	public function __construct() {
		/** @var \Phalcon\Mvc\Router $router */
		self::$router = Di::getDefault()->get('router');
	}

	/**
	 * @return bool
	 */
	public function isExternal() {
		return false;
	}

	/**
	 * @return string
	 */
	public function getURL() {
		return $this->link;
	}

	/**
	 * @return bool
	 */
	public function isActive() {
		if (is_object(self::$router)) {
			return self::$router->getRewriteUri() == $this->link;
		}
		return false;
	}
}