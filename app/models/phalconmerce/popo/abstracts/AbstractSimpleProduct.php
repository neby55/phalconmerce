<?php

namespace Phalconmerce\Popo\Abstracts;


abstract class AbstractSimpleProduct extends AbstractModel {

	public function initialize() {
		parent::initialize();
		$this->coreType = self::PRODUCT_TYPE_SIMPLE;
	}
}