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

	const CORE_TYPE_SIMPLE_PRODUCT = 1;
	const CORE_TYPE_CONFIGURABLE_PRODUCT = 2;
	const CORE_TYPE_GROUPED_PRODUCT = 3;


	public function setExtendedClassNameFromCoreTypeResponse($coreProductType) {
		if ($coreProductType == self::CORE_TYPE_SIMPLE_PRODUCT) {
			$this->extendedClassName = 'AbstractSimpleProduct';
		}
		else if ($coreProductType == self::CORE_TYPE_CONFIGURABLE_PRODUCT) {
			$this->extendedClassName = 'AbstractConfigurableProduct';
		}
		else if ($coreProductType == self::CORE_TYPE_GROUPED_PRODUCT) {
			$this->extendedClassName = 'AbstractGroupedProduct';
		}
	}
}