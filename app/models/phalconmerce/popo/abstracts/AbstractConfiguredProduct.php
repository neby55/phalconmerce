<?php

namespace Phalconmerce\Popo\Abstracts;


abstract class AbstractConfiguredProduct extends AbstractModel {

	public function initialize() {
		parent::initialize();
		$this->coreType = self::PRODUCT_TYPE_CONFIGURED;
	}
}