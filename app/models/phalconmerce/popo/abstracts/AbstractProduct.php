<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

/**
 * Class AbstractProduct
 * @package Phalconmerce\Popo\Abstracts
 */
abstract class AbstractProduct extends AbstractModel {
	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="integer", length=1, nullable=false)
	 * @var int
	 */
	public $coreType;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $sku;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $priceVatExcluded;

	/**
	 * @Column(type="float", nullable=true)
	 * @var float
	 */
	public $weight;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $stock;

	/**
	 * @Column(type="string", length=64, nullable=false)
	 * @Translate
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @Translate
	 * @var string
	 */
	public $shortDescription;

	/**
	 * @Column(type="text", nullable=true)
	 * @Translate
	 * @var string
	 */
	public $description;

	/**
	 * @Column(type="integer", length=2, nullable=true)
	 * @var int
	 */
	public $status;

	/**
	 * @var array
	 */
	public static $typesList = array(
		self::PRODUCT_TYPE_SIMPLE => 'Simple',
		self::PRODUCT_TYPE_CONFIGURABLE => 'Configurable',
		self::PRODUCT_TYPE_CONFIGURED => 'Configured',
		self::PRODUCT_TYPE_GROUPED => 'Grouped',
	);

	const PRODUCT_TYPE_SIMPLE = 1;
	const PRODUCT_TYPE_CONFIGURABLE = 2;
	const PRODUCT_TYPE_CONFIGURED = 3;
	const PRODUCT_TYPE_GROUPED = 4;

	public function initialize() {
		$this->setSource("product");
	}

	/**
	 * @return bool
	 */
	public function isOrderable() {
		switch ($this->coreType) {
			case self::PRODUCT_TYPE_SIMPLE :
			case self::PRODUCT_TYPE_CONFIGURED :
			case self::PRODUCT_TYPE_GROUPED :
				return true;
			default :
				return false;
		}
	}

	/**
	 * @return bool
	 */
	public function isSearchable() {
		switch ($this->coreType) {
			case self::PRODUCT_TYPE_SIMPLE :
			case self::PRODUCT_TYPE_CONFIGURABLE :
			case self::PRODUCT_TYPE_GROUPED :
				return true;
			default :
				return false;
		}
	}
}