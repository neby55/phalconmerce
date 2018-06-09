<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Db\Column;
use Phalconmerce\Models\AbstractModel;
use Phalconmerce\Models\Utils;

abstract class AbstractConfigurableProduct extends AbstractFinalProduct {
	/**
	 * @var \Phalconmerce\Models\Popo\Abstracts\AbstractConfiguredProduct[]
	 */
	public $configuredProductList;

	/**
	 * @return mixed
	 */
	protected static function getConfiguredClassName($classname) {
		return str_replace('Configurable', 'Configured', $classname);
	}

	public function loadConfiguredProducts() {
		$this->configuredProductList = array();
		if ($this->id > 0) {
			$fqcn = static::getConfiguredClassName(get_class($this));
			$resultSet = $fqcn::find(
				array(
					'conditions' => 'fk_configurable_product_id = :configurableProductId:',
					'bind' => array(
						'configurableProductId' => $this->id
					),
					'bindTypes' => array(
						Column::BIND_PARAM_INT
					)
				)
			);
			if (!empty($resultSet) && $resultSet->count() > 0) {
				/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractConfiguredProduct $currentConfiguredProduct */
				foreach ($resultSet as $currentConfiguredProduct) {
					$this->configuredProductList[] = $currentConfiguredProduct;
				}
			}
		}
	}
}