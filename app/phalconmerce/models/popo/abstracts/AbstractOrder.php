<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractOrder extends AbstractModel {

	const STATUS_NONE = 0;
	const STATUS_VALIDATED = 1;
	const STATUS_PAID = 2;
	const STATUS_PARTIALLY_PAID = 3;
	const STATUS_PENDING = 4;
	const STATUS_SHIPPED = 6;
	const STATUS_RECEIVED = 8;
	const STATUS_RETURNED = 10;
	const STATUS_CANCELED = 21;

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=3, nullable=false)
	 * @var string
	 */
	public $fk_currency_id;

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
	public $fk_voucher_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_delivery_delay_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_payment_method_id;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $amountDiscount;

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
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $validationDate;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/**
	 * Static method returning possibles datas in <select> tag for the field "type"
	 * @return array
	 */
	public static function statusSelectOptions() {
		return array(
			0 => 'choose',
			static::STATUS_NONE => 'none',
			static::STATUS_PARTIALLY_PAID => 'partially paid',
			static::STATUS_PAID => 'paid',
			static::STATUS_PENDING => 'preparation pending',
			static::STATUS_SHIPPED => 'shipped',
			static::STATUS_RECEIVED => 'received',
			static::STATUS_RETURNED => 'returned',
			static::STATUS_CANCELED => 'canceled',
		);
	}

	/**
	 * @return bool
	 */
	public function canAcceptPayment() {
		// True if amount > 0 AND status is "none" or "partially paid"
		return $this->amountVatExcluded > 0 && in_array($this->status, array(static::STATUS_PARTIALLY_PAID, static::STATUS_NONE));
	}

	/**
	 * @return bool
	 */
	public function isFree() {
		// true if the amount is 0 AND the discount total is > 0 AND status is "none"
		return $this->amountVatExcluded == 0 && $this->amountDiscount > 0 && in_array($this->status, array(static::STATUS_NONE));
	}
}