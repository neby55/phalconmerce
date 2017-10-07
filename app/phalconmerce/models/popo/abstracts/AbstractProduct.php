<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalconmerce\Models\AbstractDesignedModel;
use Phalconmerce\Models\Popo\Generators\Popo\PhpClass;
use Phalconmerce\Models\Popo\Image;
use Phalconmerce\Models\Popo\Product;
use Phalconmerce\Models\Utils;

/**
 * Class AbstractProduct
 * @package Phalconmerce\Models\Popo\Abstracts
 */
abstract class AbstractProduct extends AbstractDesignedModel {
	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_attributeset_id;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_manufacturer_id;

	/**
	 * @Column(type="integer", length=1, nullable=false, editable=false)
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
	 * @Column(type="timestamp", nullable=true)
	 * @var string
	 */
	public $newsFromDate;

	/**
	 * @Column(type="timestamp", nullable=true)
	 * @var string
	 */
	public $newsToDate;

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

	/**
	 * @return Image
	 */
	public function getFirstImage() {
		/** @var \Phalcon\Mvc\Model\Resultset $imageObject */
		$imageResult = $this->getImage(array(
			'order' => 'position',
			'limit' => 1
		));
		if (!empty($imageResult) && $imageResult->count() > 0) {
			return $imageResult->getFirst();
		}
		return new Image();
	}

	/**
	 * Methods that return correct Object (simple, configrable, etc.) for given id
	 * @param $id
	 * @return \Phalconmerce\Models\Popo\Abstracts\AbstractProduct
	 */
	public static function getProductById($id) {
		/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractProduct $product */
		$product = Product::findFirstById($id);

		if (!empty($product)) {
			return $product->getFinalProductObject();
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getFinalProductClassName() {
		$classname = '';
		switch ($this->coreType) {
			case self::PRODUCT_TYPE_SIMPLE :
				$classname = 'SimpleProduct';
				break;
			case self::PRODUCT_TYPE_CONFIGURABLE :
				$classname = 'ConfigurableProduct';
				break;
			case self::PRODUCT_TYPE_CONFIGURED :
				$classname = 'ConfiguredProduct';
				break;
			case self::PRODUCT_TYPE_GROUPED :
				$classname = 'GroupedProduct';
				break;
		}
		return $classname;
	}

	/**
	 * @return string
	 */
	public function getFinalProductFQCN() {
		$classname = $this->getFinalProductClassName();
		if (!empty($classname)) {
			return PhpClass::POPO_NAMESPACE. '\\' . $classname;
		}
		return '';
	}

	/**
	 * Methods that return correct Object (simple, configrable, etc.) for given id
	 * @return \Phalconmerce\Models\Popo\Abstracts\AbstractProduct
	 */
	public function getFinalProductObject() {
		$fqcn = $this->getFinalProductFQCN();
		if (!empty($fqcn)) {
			$object = $fqcn::findFirst('fk_product_id = '.$this->id);
			if (empty($object)) {
				$object = new $fqcn;
				$object->fk_product_id = $this->id;
			}
			return $object;
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getCoreTypeLabel() {
		if (array_key_exists($this->coreType, self::$typesList)) {
			return self::$typesList[$this->coreType];
		}
		return '-';
	}
}