<?php

namespace Phalconmerce\Popo\Abstracts;


abstract class AbstractConfiguredProduct extends AbstractProduct {

	public function initialize() {
		parent::initialize();
		$this->coreType = self::PRODUCT_CORE_TYPE_CONFIGURED;
	}
}