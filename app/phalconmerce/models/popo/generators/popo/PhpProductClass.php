<?php

namespace Phalconmerce\Models\Popo\Generators\Popo;

use Phalcon\Di;

class PhpProductClass extends PhpClass {

	/** @var array */
	protected static $abstractProductClassesList = array(
		'AbstractConfigurableProduct',
		'AbstractConfiguredProduct',
		'AbstractSimpleProduct',
		'AbstractGroupedProduct',
		'AbstractGroupedProductHasProduct'
	);
	/** @var array */
	protected static $productClassName = array(
		1 => 'ProductSimple%s',
		2 => 'ProductConfigurable%s',
		3 => 'ProductGrouped%s',
		4 => 'ProductConfigured%s',
		5 => 'ProductGrouped%sHasProduct'
	);

	/** @var int */
	protected $coreType;

	const CORE_TYPE_SIMPLE_PRODUCT = 1;
	const CORE_TYPE_CONFIGURABLE_PRODUCT = 2;
	const CORE_TYPE_GROUPED_PRODUCT = 3;
	const CORE_TYPE_CONFIGURED_PRODUCT = 4;
	const CORE_TYPE_GROUPED_PRODUCT_HAS_PRODUCT = 5;

	public function __construct($className, $coreType) {
		$this->coreType = $coreType;
		// Adding ProductSimple, ProductGrouped, etc., to class name
		$className = $this->getPrefixedClassName($className);
		parent::__construct($className, self::getExtendedClassNameFromCoreTypeResponse($this->coreType));

		$this->setupRelationships();
	}

	public function setExtendedClassNameFromCoreTypeResponse($coreProductType) {
		$extendedClassName = self::getExtendedClassNameFromCoreTypeResponse($coreProductType);
		if ($extendedClassName !== false) {
			$this->extendedClassName = $extendedClassName;
		}
	}

	/**
	 * @param int $coreProductType
	 * @return bool|string
	 */
	public static function getExtendedClassNameFromCoreTypeResponse($coreProductType) {
		if ($coreProductType == self::CORE_TYPE_SIMPLE_PRODUCT) {
			return 'AbstractSimpleProduct';
		}
		else if ($coreProductType == self::CORE_TYPE_CONFIGURABLE_PRODUCT) {
			return 'AbstractConfigurableProduct';
		}
		else if ($coreProductType == self::CORE_TYPE_CONFIGURED_PRODUCT) {
			return 'AbstractConfiguredProduct';
		}
		else if ($coreProductType == self::CORE_TYPE_GROUPED_PRODUCT) {
			return 'AbstractGroupedProduct';
		}
		else if ($coreProductType == self::CORE_TYPE_GROUPED_PRODUCT_HAS_PRODUCT) {
			return 'AbstractGroupedProductHasProduct';
		}
		return false;
	}

	/**
	 * @param string $className
	 * @return bool|string
	 */
	public function getPrefixedClassName($className) {
		return self::getPrefixedClassNameFromCoreType($this->coreType, $className);
	}

	/**
	 * @param int $coreProductType
	 * @param string $className
	 * @return bool|string
	 */
	public static function getPrefixedClassNameFromCoreType($coreProductType, $className) {
		if (array_key_exists($coreProductType, self::$productClassName)) {
			return sprintf(self::$productClassName[$coreProductType], $className);
		}
		return false;
	}

	private function setupRelationships() {
		$this->relationshipsList = array();
		if ($this->coreType == self::CORE_TYPE_CONFIGURABLE_PRODUCT) {
			// Relation 1 Configurable => N Configured
			$propertyName = 'fk_configurableproduct_id';
			$this->relationshipsList['id'] = new Relationship(
				'id',
				$this->className,
				$propertyName,
				self::POPO_NAMESPACE.'\\'.str_replace('ProductConfigurable', 'ProductConfigured', $this->className),
				Relationship::TYPE_1_TO_MANY
			);
		}
		else if ($this->coreType == self::CORE_TYPE_CONFIGURED_PRODUCT) {
			// Relation N Configured => 1 Configurable
			$propertyName = 'fk_configurableproduct_id';
			$this->relationshipsList[$propertyName] = new Relationship(
				$propertyName,
				$this->className,
				'id',
				self::POPO_NAMESPACE.'\\'.str_replace('ProductConfigured', 'ProductConfigurable', $this->className),
				Relationship::TYPE_MANY_TO_1
			);
		}
		else if ($this->coreType == self::CORE_TYPE_GROUPED_PRODUCT) {
			// RelationshipManyToMany  N Grouped => M Products
			/*From DeliveryDelay
			$this->hasManyToMany(
				"id", // 3
				"Phalconmerce\\Models\\Popo\\DeliveryDelayHasCountry", // 5
				"fk_deliverydelay_id", // 1
				"fk_country_id", // 6
				"Phalconmerce\\Models\\Popo\\Country", // 7
				"id" // 4
			);*/
			$this->relationshipsList[self::POPO_NAMESPACE.'\Product'] = new RelationshipManyToMany (
				'fk_groupedproduct_id',
				self::POPO_NAMESPACE.'\\'.$this->className,
				'id',
				'id',
				self::POPO_NAMESPACE.'\\'.$this->className.'HasProduct',
				'fk_product_id',
				self::POPO_NAMESPACE.'\\'.'Product'
			);
		}
		else if ($this->coreType == self::CORE_TYPE_GROUPED_PRODUCT_HAS_PRODUCT) {
			// Relation N Configured => 1 Configurable
			$propertyName = 'fk_groupedproduct_id';
			$this->relationshipsList[$propertyName] = new Relationship(
				$propertyName,
				$this->className,
				'id',
				self::POPO_NAMESPACE.'\\'.str_replace('HasProduct', '', $this->className),
				Relationship::TYPE_MANY_TO_1
			);
			// Relation N Configured => 1 Configurable
			$propertyName = 'fk_product_id';
			$this->relationshipsList[$propertyName] = new Relationship(
				$propertyName,
				$this->className,
				'id',
				self::POPO_NAMESPACE.'\\'.'Product',
				Relationship::TYPE_MANY_TO_1
			);
		}
	}

	/***
	 * @return bool|int
	 */
	public function getSecondClassNameCoreType() {
		if ($this->coreType == self::CORE_TYPE_CONFIGURABLE_PRODUCT) {
			return self::CORE_TYPE_CONFIGURED_PRODUCT;
		}
		else if ($this->coreType == self::CORE_TYPE_GROUPED_PRODUCT) {
			return self::CORE_TYPE_GROUPED_PRODUCT_HAS_PRODUCT;
		}
		return false;
	}

	/***
	 * @return bool
	 */
	public function isSecondClassNeedsProperties() {
		if ($this->coreType == self::CORE_TYPE_CONFIGURABLE_PRODUCT) {
			return true;
		}
		return false;
	}

	/**
	 * @param string $propertyName
	 * @param \Phalconmerce\Models\Popo\Generators\Popo\Relationship $relationship
	 */
	public function addRelationship($propertyName, $relationship) {
		if (is_a($relationship, 'Phalconmerce\Models\Popo\Generators\Popo\Relationship')) {
			$this->relationshipsList[$propertyName] = $relationship;
		}
	}
}