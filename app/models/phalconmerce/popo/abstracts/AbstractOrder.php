<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;


abstract class AbstractOrder extends AbstractModel {
	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	// TODO make fk for currency code

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_lang_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_address_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_customer_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_vouncher_id;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $discount;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $amountVatExcl;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $amountVatIncl;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $paymentDate;

	/**
	 * @Column(type="integer", length=2, nullable=false)
	 * @var int
	 */
	public $paymentType;

	/**
	 * @Column(type="boolean", nullable=false)
	 * @var int
	 */
	public $isGift;

	/**
	 * @Column(type="text", nullable=false)
	 * @var string
	 */
	public $giftMessage;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}