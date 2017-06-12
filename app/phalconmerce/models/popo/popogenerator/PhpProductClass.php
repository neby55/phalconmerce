<?php

namespace Phalconmerce\Models\Popo\Popogenerator;

use Phalcon\Di;

class PhpProductClass extends PhpClass {

	/** @var array */
	protected static $abstractProductClassesList = array(
		'AbstractConfigurableProduct',
		'AbstractConfiguredProduct',
		'AbstractSimpleProduct',
		'AbstractGroupedProduct',
		'AbstractGroupedProductHasSimpleProduct'
	);
	/** @var array */
	protected static $productClassNamePrefix = array(
		1 => 'ProductSimple',
		2 => 'ProductConfigurable',
		3 => 'ProductGrouped',
		4 => 'ProductConfigured'
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
		$classNamePrefix = $this->getClassNamePrefix();
		if ($classNamePrefix !== false) {
			$className = $classNamePrefix.$className;
		}
		parent::__construct($className, self::getExtendedClassNameFromCoreTypeResponse($this->coreType));
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
		return false;
	}

	/**
	 * @return bool|string
	 */
	public function getClassNamePrefix() {
		return self::getClassNamePrefixFromCoreType($this->coreType);
	}

	/**
	 * @param int $coreProductType
	 * @return bool|string
	 */
	public static function getClassNamePrefixFromCoreType($coreProductType) {
		if (array_key_exists($coreProductType, self::$productClassNamePrefix)) {
			return self::$productClassNamePrefix[$coreProductType];
		}
		return false;
	}

	public function getSecondClassNameCoreType() {
		if ($this->coreType == self::CORE_TYPE_SIMPLE_PRODUCT) {
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
	}
}