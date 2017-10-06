<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Db\Column;
use Phalconmerce\Models\AbstractModel;

abstract class AbstractConfigurableProduct extends AbstractFinalProduct {
	/**
	 * @var \Phalconmerce\Models\Popo\Abstracts\AbstractConfiguredProduct[]
	 */
	public $configuredProductList;

	/**
	 * @return mixed
	 */
	protected static function getConfiguredClassName() {
		return str_replace('Configurable', 'Configured', __CLASS__);
	}

	private function loadConfiguredProducts() {
		if ($this->id > 0) {
			$fqcn = self::getConfiguredClassName();
			$tmpObject = new $fqcn();
			$this->configuredProductList = $fqcn::find(
				array(
					'conditions' => $tmpObject->prefix . 'fk_configurableproduct_id = :configurableProductId:',
					'bind' => array(
						'configurableProductId' => $this->id
					),
					'bindTypes' => array(
						Column::BIND_PARAM_INT
					)
				)
			);
		}
	}

	public function initialize() {
		parent::initialize();

		$this->loadConfiguredProducts();
	}
}