<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 03/02/2017
 * Time: 12:04
 */

namespace Phalconmerce\Popo\Abstracts;


abstract class AbstractGroupedProduct extends AbstractProduct {

	public function initialize() {
		parent::initialize();
		$this->coreType = self::PRODUCT_CORE_TYPE_GROUPED;
	}
}