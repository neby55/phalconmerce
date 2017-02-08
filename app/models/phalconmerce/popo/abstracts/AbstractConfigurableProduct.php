<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 03/02/2017
 * Time: 12:03
 */

namespace Phalconmerce\Popo\Abstracts;


abstract class AbstractConfigurableProduct extends AbstractProduct {

	public function initialize() {
		parent::initialize();
		$this->coreType = self::PRODUCT_CORE_TYPE_CONFIGURABLE;
	}
}