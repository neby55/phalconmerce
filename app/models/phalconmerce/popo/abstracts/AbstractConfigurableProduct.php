<?php

namespace Phalconmerce\Popo\Abstracts;


abstract class AbstractConfigurableProduct extends AbstractProduct {

	public function initialize() {
		parent::initialize();
		$this->coreType = self::PRODUCT_CORE_TYPE_CONFIGURABLE;
	}
}