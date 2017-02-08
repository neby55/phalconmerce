<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 03/02/2017
 * Time: 12:02
 */

namespace Phalconmerce\Popo\Abstracts;


abstract class AbstractSimpleProduct extends AbstractProduct {

	public function initialize() {
		parent::initialize();
		$this->coreType = self::PRODUCT_CORE_TYPE_SIMPLE;
	}
}