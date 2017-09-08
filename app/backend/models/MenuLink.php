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
}