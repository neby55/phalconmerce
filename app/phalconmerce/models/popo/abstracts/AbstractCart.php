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
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_cart_id;

	/**
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
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_product_id;

	/**
	 * @Column(type="integer", nullable=true)
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
		return \Phalconmerce\Models\Popo\Cart::findFirst($this->getParentId());
	}

	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->fk_cart_id;
	}

	/**
	 * @return float
	 */
	public function getTotalVatExcluded() {
		return $this->amountVatExcluded * $this->quantity;
	}

	/**
	 * @return float
	 */
	public function getTotalVatIncluded() {
		return $this->amountVatIncluded * $this->quantity;
	}

	/**
	 * @return float
	 */
	public function getTotalTax() {
		return ($this->amountVatIncluded - $this->amountVatExcluded) * $this->quantity;
	}
}