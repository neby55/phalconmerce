<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractVoucher extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=64, nullable=false)
	 * @var string
	 * @Index
	 */
	public $code;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $type;

	/**
	 * @Column(type="date", nullable=true)
	 * @var string
	 */
	public $startDate;

	/**
	 * @Column(type="date", nullable=true)
	 * @var string
	 */
	public $endDate;

	/**
	 * @Column(type="datetime", nullable=true)
	 * @var string
	 */
	public $useDate;

	/**
	 * @Column(type="float", nullable=true)
	 * @var float
	 */
	public $amountVatExcluded;

	/**
	 * @Column(type="float", nullable=true)
	 * @var float
	 */
	public $percent;

	/**
	 * @Column(type="float", nullable=false, default=1)
	 * @var float
	 */
	public $minimumAmountVatExcluded;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $emailRestriction;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $maximumUse;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	const TYPE_PERCENT = 1;
	const TYPE_FIXED_AMOUNT = 2;
	const TYPE_PERCENT_WITH_MIN_AMOUNT = 3;
	const TYPE_FIXED_AMOUNT_WITH_MIN_AMOUNT = 4;
	const TYPE_FRIEND_SPONSORING = 5;

	public static function randomCode($length=8) {
		$code = '';
		$chars = 'AKL4M3WOPXCVRTYU8Z2EQ67SDFGH9JB5N';
		for ($i=0;$i<$length;$i++) {
			$code .= $chars[mt_rand(0, strlen($chars)-1)];
		}
		return $code;
	}

	/**
	 * Static method returning possibles datas in <select> tag for the field "status"
	 * @return array
	 */
	public static function statusSelectOptions() {
		return array(
			0 => '-',
			1 => 'active',
			2 => 'in cart',
			3 => 'ordered',
			4 => 'used',
			9 => 'canceled'
		);
	}
}