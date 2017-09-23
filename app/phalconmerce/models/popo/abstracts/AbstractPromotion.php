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

	// TODO find a way to define available promotion types (const no, static maybe, function last option)

	/**
	 * Overriding AbstractModel::initialize() to force the prefix
	 */
	public function initialize() {
		// Set the prefix
		$this->prefix = 'pmt_';

		parent::initialize();
	}

	/**
	 * @param float $price
	 * @return bool|float
	 */
	public function getPromotionalPrice($price) {
		if (is_numeric($price)) {
			// casting $price
			$price = (float) $price;
			if ($this->type == self::TYPE_AMOUNT) {
				return $price - $this->value;
			}
			else if ($this->type == self::TYPE_PERCENT) {
				return $price - ($price * $this->value / 100);
			}
			return $price;
		}
		return false;
	}

}