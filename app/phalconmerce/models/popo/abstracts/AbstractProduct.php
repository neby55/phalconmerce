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
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $sku;

	/**
	 * @Column(type="string", length=64, nullable=false)
	 * @Translate
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_attribute_set_id;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_manufacturer_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_tax_id;

	/**
	 * @Column(type="integer", length=1, nullable=false, editable=false)
	 * @var int
	 */
	public $coreType;

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
	 * @Column(type="timestamp", nullable=true, default='0000-00-00 00:00:00')
	 * @var string
	 */
	public $newsFromDate;

	/**
	 * @Column(type="timestamp", nullable=true, default='0000-00-00 00:00:00')
	 * @var string
	 */
	public $newsToDate;

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

	const PRODUCT_TYPE_SIMPLE = 1;
	const PRODUCT_TYPE_CONFIGURABLE = 2;
	const PRODUCT_TYPE_CONFIGURED = 3;
	const PRODUCT_TYPE_GROUPED = 4;

	public static function getTypesList() {
		return  array(
			static::PRODUCT_TYPE_SIMPLE => 'Simple',
			static::PRODUCT_TYPE_CONFIGURABLE => 'Configurable',
			static::PRODUCT_TYPE_CONFIGURED => 'Configured',
			static::PRODUCT_TYPE_GROUPED => 'Grouped',
		);
	}

	/**
	 * @return bool
	 */
	public function isOrderable() {
		switch ($this->coreType) {
			case static::PRODUCT_TYPE_SIMPLE :
			case static::PRODUCT_TYPE_CONFIGURED :
			case static::PRODUCT_TYPE_GROUPED :
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
			case static::PRODUCT_TYPE_SIMPLE :
			case static::PRODUCT_TYPE_CONFIGURABLE :
			case static::PRODUCT_TYPE_GROUPED :
				return true;
			default :
				return false;
		}
	}

	/**
	 * @return bool
	 */
	public function isConfigurable() {
		return $this->coreType == static::PRODUCT_TYPE_CONFIGURABLE;
	}

	/**
	 * @return bool
	 */
	public function isConfigured() {
		return $this->coreType == static::PRODUCT_TYPE_CONFIGURED;
	}

	/**
	 * @return bool
	 */
	public function isGrouped() {
		return $this->coreType == static::PRODUCT_TYPE_GROUPED;
	}

	/**
	 * @return bool
	 */
	public function isSimple() {
		return $this->coreType == static::PRODUCT_TYPE_SIMPLE;
	}

	/**
	 * @return bool
	 */
	public function isActive() {
		return $this->status == 1;
	}

	/**
	 * @return Image
	 */
	public function getFirstImage() {
		// ConfiguredProduct exception
		if ($this->isConfigured()) {
			/** @var AbstractConfiguredProduct $configuredProduct */
			$configuredProduct = $this->getFinalProductObject();
			if (!empty($configuredProduct)) {
				$configuredProduct->loadConfigurableProduct();
				$configurableProduct = $configuredProduct->getConfigurableProduct();
				if (!empty($configurableProduct)) {
					/** @var AbstractProduct $sourceProduct */
					$sourceProduct = $configurableProduct->getRelatedProduct();
				}
			}
		}
		else {
			/** @var AbstractProduct $sourceProduct */
			$sourceProduct = $this;
		}
		if (!empty($sourceProduct)) {
			/** @var \Phalcon\Mvc\Model\Resultset $imageObject */
			$imageResult = $sourceProduct->getImage(array(
				'order' => 'position',
				'limit' => 1
			));
			if (!empty($imageResult) && $imageResult->count() > 0) {
				return $imageResult->getFirst();
			}
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
			case static::PRODUCT_TYPE_SIMPLE :
				$classname = 'SimpleProduct';
				break;
			case static::PRODUCT_TYPE_CONFIGURABLE :
				$classname = 'ConfigurableProduct';
				break;
			case static::PRODUCT_TYPE_CONFIGURED :
				$classname = 'ConfiguredProduct';
				break;
			case static::PRODUCT_TYPE_GROUPED :
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
		$typesList = static::getTypesList();
		if (array_key_exists($this->coreType, $typesList)) {
			return $typesList[$this->coreType];
		}
		return '-';
	}

	/**
	 * @return int[]
	 */
	public function getProductIdsForFilters() {
		$idsList = array($this->id => $this->id);

		// If GroupedProduct
		if ($this->isGrouped()) {
			/** @var AbstractGroupedProduct $finalProduct */
			$finalProduct = $this->getFinalProductObject();
			$finalProduct->loadRelatedProducts();
			/** @var AbstractProduct $currentProduct */
			foreach ($finalProduct->childrenProductList as $currentProduct) {
				$idsList[$currentProduct->id] = $currentProduct->id;
			}
		}
		// If ConfigurableProduct
		else if ($this->isConfigurable()) {
			/** @var AbstractConfigurableProduct $finalProduct */
			$finalProduct = $this->getFinalProductObject();
			$finalProduct->loadConfiguredProducts();
			/** @var AbstractConfiguredProduct $currentConfiguredProduct */
			foreach ($finalProduct->configuredProductList as $currentConfiguredProduct) {
				$currentProduct = $currentConfiguredProduct->getRelatedProduct();
				$idsList[$currentProduct->id] = $currentProduct->id;
			}
		}
		// If ConfiguredProduct
		else if ($this->isConfigured()) {
			/** @var AbstractConfiguredProduct $finalProduct */
			$finalProduct = $this->getFinalProductObject();
			$finalProduct->loadConfigurableProduct();
			$currentProduct = $finalProduct->getConfigurableProduct()->getRelatedProduct();
			$idsList[$currentProduct->id] = $currentProduct->id;
		}
		return $idsList;
	}

	/**
	 * @return float
	 */
	public function getPriceVatIncluded() {
		/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractTax $taxObject */
		$taxObject = $this->getTax();
		if (!empty($taxObject)) {
			return $this->priceVatExcluded + ($this->priceVatExcluded * $taxObject->percent / 100);
		}
		return $this->priceVatExcluded;
	}

	/**
	 * @return float
	 */
	public function getPriceVatExcluded() {
		return $this->priceVatExcluded;
	}
}