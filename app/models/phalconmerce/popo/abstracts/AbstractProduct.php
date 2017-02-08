<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 03/02/2017
 * Time: 11:26
 */

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
	protected $id;
	/**
	 * @Column(type="integer", length=1, nullable=false)
	 * @var int
	 */
	protected $coreType;
	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	protected $sku;
	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	protected $price_vat_excluded;
	/**
	 * @Column(type="float", nullable=true)
	 * @var float
	 */
	protected $weight;
	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	protected $stock;
	/**
	 * @Column(type="integer", length=1, nullable=true)
	 * @var int
	 */
	protected $status;
	/**
	 * @Column(type="integer", nullable=true)
	 * @var AbstractProduct
	 */
	protected $parent_product_id;

	/**
	 * @var AbstractProductFilter[]
	 */
	protected $filtersList;

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
	 * @return AbstractProduct
	 */
	public function getParentProduct() {
		return $this->parentProduct;
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