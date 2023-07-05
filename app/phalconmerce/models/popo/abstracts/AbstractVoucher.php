<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Di;
use Phalcon\Logger;
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
	 * @Column(type="integer", length=10, nullable=false, default=0)
	 * @var int
	 */
	public $maximumUse;

	/**
	 * @Column(type="integer", length=2, nullable=true)
	 * @var int
	 */
	public $isCumulative;

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
	const TYPE_FREE_SHIPPING = 6;

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

	/**
	 * @param float $cartTotalAmount
	 * @param float $shippingAmount
	 * @return float
	 */
	public function getCartTotalReduction($cartTotalAmount, $shippingAmount=0.0) {
		// TODO manage the counter to avoid the reduction if voucher already used
		if ($this->type == static::TYPE_FIXED_AMOUNT) {
			return $this->amountVatExcluded;
		}
		else if ($this->type == static::TYPE_FIXED_AMOUNT_WITH_MIN_AMOUNT) {
			if ($cartTotalAmount >= $this->minimumAmountVatExcluded) {
				return $this->amountVatExcluded;
			}
		}
		else if ($this->type == static::TYPE_PERCENT || $this->type == static::TYPE_FRIEND_SPONSORING) {
			// TODO exclude promotional product for the percent reduction
			// => create a cartAmountWithPromo method or property
			return $cartTotalAmount * $this->percent / 100;
		}
		else if ($this->type == static::TYPE_PERCENT_WITH_MIN_AMOUNT) {
			// TODO exclude promotional product for the percent reduction
			if ($cartTotalAmount >= $this->minimumAmountVatExcluded) {
				return $cartTotalAmount * $this->percent / 100;
			}
		}
		else if ($this->type == static::TYPE_FREE_SHIPPING) {
			return (float) $shippingAmount;
		}

		return 0;
	}

	public static function isCumulativeSelectOptions() {
		return array(
			0 => '-',
			1 => Di::getDefault()->get('backendService')->t('Yes'),
			2 => Di::getDefault()->get('backendService')->t('No'),
		);
	}

	protected static function getIntValueIfPossible($value) {
		return str_replace('.00', '', $value);
	}

	/**
	 * This method should be override
	 * @return string
	 */
	public function getLabel() {
		if ($this->type == static::TYPE_FIXED_AMOUNT_WITH_MIN_AMOUNT) {
			return 'min amount = ' . static::getIntValueIfPossible($this->minimumAmountVatExcluded);
		}
		else if ($this->type == static::TYPE_PERCENT_WITH_MIN_AMOUNT) {
			return '-'.static::getIntValueIfPossible($this->percent).'% with min amount = ' . static::getIntValueIfPossible($this->minimumAmountVatExcluded);
		}
		else if ($this->type == static::TYPE_FREE_SHIPPING) {
			return 'free shipping';
		}
		else if ($this->type == static::TYPE_PERCENT) {
			return '-'.static::getIntValueIfPossible($this->percent).'%';
		}
		return '';
	}

	/**
	 * @return int
	 */
	public function getValidationCode() {
		// Search for not valid cases
		$startDate = strtotime($this->startDate);
		$endDate = strtotime($this->endDate);

		if ($this->status != 1) {
			return 2;
		}
		if ($startDate > 0 && $startDate > time()) {
			return 3;
		}
		if ($endDate > 0 && $endDate < time()) {
			return 4;
		}
		// TODO check email validation
		// TODO check max use of voucher

		return 1;
	}

	/**
	 * This method should be override
	 * @param int
	 * @return int
	 */
	public static function getValidationLabel($validationCode) {
		if ($validationCode == 1) {
			return 'Voucher added';
		}
		if ($validationCode == 2) {
			return 'Voucher code is not recognized';
		}
		if ($validationCode == 3) {
			return 'Voucher is not enabled yet';
		}
		if ($validationCode == 4) {
			return 'Voucher has expired';
		}
		return '';
	}
}