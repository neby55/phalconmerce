<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\AbstractModel;

abstract class AbstractPayment extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_order_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_payment_method_id;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $paymentDate;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $paymentAmount;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $refundDate;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $refundAmount;

	/**
	 * @Column(type="text", nullable=true)
	 * @var string
	 */
	public $params;
}