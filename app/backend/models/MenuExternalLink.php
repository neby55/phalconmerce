<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Models;


class MenuExternalLink extends MenuLink {
	public function __construct($link) {
		parent::__construct();
		if (filter_var($link, FILTER_VALIDATE_URL) !== false) {
			$this->link = $link;
		}
	}

	/**
	 * @return bool
	 */
	public function isExternal() {
		return true;
	}

	/**
	 * @return bool
	 */
	public function isActive() {
		return false;
	}

}