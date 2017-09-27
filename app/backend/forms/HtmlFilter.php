<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Forms;

use Phalcon\FilterInterface;

class HtmlFilter implements FilterInterface{
	/**
	 * Adds a user-defined filter
	 *
	 * @param string $name
	 * @param mixed $handler
	 * @return FilterInterface
	 */
	public function add($name, $handler) {

	}

	/**
	 * Sanizites a value with a specified single or set of filters
	 *
	 * @param mixed $value
	 * @param mixed $filters
	 * @return mixed
	 */
	public function sanitize($value, $filters) {
		// TODO: Implement sanitize() method.
	}

	/**
	 * Return the user-defined filters in the instance
	 *
	 * @return array
	 */
	public function getFilters() {
		// TODO: Implement getFilters() method.
	}

}