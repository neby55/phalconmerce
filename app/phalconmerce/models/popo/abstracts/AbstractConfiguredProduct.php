<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractConfiguredProduct extends AbstractFinalProduct {
	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_configurableproduct_id;

	/**
	 * @var \Phalconmerce\Models\Popo\ConfigurableProduct
	 */
	public $configurableProduct;

	public function initialize() {
		parent::initialize();

		$this->loadConfigurableProduct();
	}

	/**
	 * @return mixed
	 */
	public static function getConfigurableClassName() {
		return str_replace('Configured', 'Configurable', __CLASS__);
	}

	private function loadConfigurableProduct() {
		$fqcn = self::getConfigurableClassName();
		$this->configurableProduct = $fqcn::findFirst($this->getConfigurableProductId());
	}

	/**
	 * @return int
	 */
	public function getConfigurableProductId() {
		return $this->fk_configurableproduct_id;
	}

	/**
	 * @return \Phalconmerce\Models\Popo\ConfigurableProduct
	 */
	public function getConfigurableProduct() {
		return $this->configurableProduct;
	}
}