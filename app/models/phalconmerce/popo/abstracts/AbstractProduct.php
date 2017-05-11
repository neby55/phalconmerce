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
	 * @Column(type="integer", length=2, nullable=true)
	 * @var int
	 */
	public $status;
	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $parentProductId;

	/**
	 * @var AbstractProductFilter[]
	 */
	public $filtersList;

	const PRODUCT_CORE_TYPE_SIMPLE = 1;
	const PRODUCT_CORE_TYPE_CONFIGURABLE = 2;
	const PRODUCT_CORE_TYPE_CONFIGURED = 3;
	const PRODUCT_CORE_TYPE_GROUPED = 4;

	public function initialize() {
		$this->setSource("product");
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getCoreType() {
		return $this->coreType;
	}

	/**
	 * @return ProductFilter[]
	 */
	public function getFiltersList() {
		return $this->filtersList;
	}

	/**
	 * @return bool
	 */
	public function isOrderable() {
		switch ($this->coreType) {
			case self::PRODUCT_CORE_TYPE_SIMPLE :
			case self::PRODUCT_CORE_TYPE_CONFIGURED :
			case self::PRODUCT_CORE_TYPE_GROUPED :
				return true;
			default :
				return false;
		}
	}
}