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

class MenuLink {
	/** @var string */
	protected $link;

	public function __construct($link) {
		$this->link = $link;
	}

	public function getURL() {
		$dependencyInjector = Di::getDefault();
		$baseURL = $dependencyInjector->get('config')->baseUri . '/' . $dependencyInjector->get('config')->adminDir . '/';
		return str_replace('//', '/', $baseURL.$this->link);
	}
}