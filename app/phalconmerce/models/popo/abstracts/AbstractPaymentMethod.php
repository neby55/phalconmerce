<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\AbstractModel;
use Phalconmerce\Models\Popo\Generators\Popo\PhpClass;

abstract class AbstractPaymentMethod extends AbstractModel {

	const TYPE_CREDIT_CARD = 1;
	const TYPE_BANK_TRANSFER = 2;
	const TYPE_CHECK = 3;
	const TYPE_FREE = 20;
	const TYPE_OTHER = 99;

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=3, nullable=true)
	 * @var string
	 */
	public $fk_currency_id;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $type;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="text", nullable=true)
	 * @var string
	 */
	public $description;

	/**
	 * @Column(type="float", nullable=true)
	 * @var float
	 */
	public $minimumAmount;

	/**
	 * @Column(type="float", nullable=true)
	 * @var float
	 */
	public $maximumAmount;

	/**
	 * @Column(type="string", length=64, nullable=true)
	 * @var string
	 */
	public $paymentSystemClassName;

	/**
	 * @Column(type="integer", length=4, nullable=true, default=999)
	 * @var int
	 */
	public $position;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/**
	 * Static method returning possibles datas in <select> tag for the field "type"
	 * @return array
	 */
	public static function typeSelectOptions() {
		return array(
			0 => 'choose',
			self::TYPE_CREDIT_CARD => 'credit card',
			self::TYPE_BANK_TRANSFER => 'bank transfer',
			self::TYPE_CHECK => 'check',
			self::TYPE_FREE => 'free',
			self::TYPE_OTHER => 'other'
		);
	}

	/**
	 * Static method returning possibles datas in <select> tag for the field "paymentSystemClassName"
	 * @return array
	 */
	public static function paymentSystemClassNameSelectOptions() {
		return array(
			0 => 'choose',
			-1 => 'dropdown has to be defined by your developer'
		);
	}

	/**
	 * @return string
	 */
	public function getPaymentSystemFQCN() {
		return '\Phalconmerce\Models\Checkout\\'.$this->paymentSystemClassName;
	}
}