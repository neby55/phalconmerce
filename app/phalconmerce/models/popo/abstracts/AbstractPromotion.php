<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractDesignedModel;

abstract class AbstractPromotion extends AbstractDesignedModel {

	const TYPE_PERCENT = 1;
	const TYPE_AMOUNT = 2;

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $type;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $value;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $startDate;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $endDate;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/**
	 * Overriding AbstractModel::initialize() to force the prefix
	 */
	public function initialize() {
		// Set the prefix
		$this->prefix = 'pmt_';

		parent::initialize();
	}

	/**
	 * Static method returning possibles datas in <select> tag for the field "type"
	 * @return array
	 */
	public static function typeSelectOptions() {
		return array(
			0 => 'choose',
			static::TYPE_PERCENT => 'percent',
			static::TYPE_AMOUNT => 'amount'
		);
	}

	/**
	 * @param float $price
	 * @return bool|float
	 */
	public function getPromotionalPrice($price) {
		if (is_numeric($price)) {
			// casting $price
			$price = (float) $price;
			if ($this->type == static::TYPE_AMOUNT) {
				return $price - $this->value;
			}
			else if ($this->type == static::TYPE_PERCENT) {
				return $price - ($price * $this->value / 100);
			}
			return $price;
		}
		return false;
	}

}