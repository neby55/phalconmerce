<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalconmerce\Models\AbstractDesignedModel;
use Phalconmerce\Models\Popo\Image;
use Phalconmerce\Models\Popo\ConfigurableProduct;
use Phalconmerce\Models\Popo\ConfiguredProduct;
use Phalconmerce\Models\Popo\SimpleProduct;
use Phalconmerce\Models\Popo\GroupedProduct;
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
	 * Use this method to get the wanted product object (CarPart, TeeShirt, etc.)
	 * @return mixed|bool
	 */
	public function getSubObject() {
		if ($this->className != '' && $this->id > 0) {
			$fqcn = '\\Phalconmerce\\Popo\\'.$this->className;
			$tmpObject = new $fqcn();
			return $fqcn::findFirst(
				array(
					'conditions' => $tmpObject->prefix.'fk_product_id = :productId:',
					'bind' => array(
						'productId' => $this->id
					),
					'bindTypes' => array(
						Column::BIND_PARAM_INT
					)
				)
			);
		}
		return false;
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
			$classname = '';
			switch ($product->coreType) {
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

			if (!empty($classname)) {
				$object = $classname::findFirstById($id);
				if (empty($object)) {
					$object = new $classname;
					$object->fk_product_id = $id;
				}
				return $object;
			}
		}
		return false;
	}
}