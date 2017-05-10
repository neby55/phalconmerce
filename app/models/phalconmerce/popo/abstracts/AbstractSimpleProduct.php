<?php

namespace Phalconmerce\Popo\Abstracts;


abstract class AbstractSimpleProduct extends AbstractProduct {

	public function initialize() {
		parent::initialize();
		$this->coreType = self::PRODUCT_CORE_TYPE_SIMPLE;
	}
}