<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

abstract class AbstractOrderReturnHasCart extends AbstractModel {

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_orderreturn_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_cart_id;

	/**
	 * @Column(type="integer", length=4, nullable=false, default=0)
	 * @var int
	 */
	public $quantity;

	/**
	 * Overriding AbstractModel::initialize() to force the prefix
	 */
	public function initialize() {
		// Set the prefix
		$this->prefix = 'orhc_';

		parent::initialize();
	}

}