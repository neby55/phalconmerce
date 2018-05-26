<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models;

class FilterData {
	/** @var int */
	public $id;

	/** @var string */
	public $value;

	/** @var string */
	public $slug;

	/**@var int[] */
	public $productIdList;

	/**
	 * @return int|string
	 */
	public function getFormValue() {
		if ($this->id > 0) {
			return $this->id;
		}
		return $this->value;
	}
}