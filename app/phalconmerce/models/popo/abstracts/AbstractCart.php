<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractCart extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_cart_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_order_id;

	/**
	 * @Column(type="string", length=3, nullable=false)
	 * @var string
	 */
	public $fk_currency_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_product_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_promotion_id;

	/**
	 * @Column(type="integer", length=4, nullable=false, default=1)
	 * @Index
	 * @var int
	 */
	public $quantity;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $amountVatExcluded;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $amountVatIncluded;

	/**
	 * @return AbstractMenu
	 */
	public function getParent() {
		return \Phalconmerce\Popo\Cart::findFirst($this->getParentId());
	}

	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->fk_cart_id;
	}
}