<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractConfiguredProduct extends AbstractFinalProduct {
	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_configurable_product_id;

	/**
	 * @Column(type="integer", length=4, nullable=true, default=99)
	 * @var int
	 */
	public $position;

	/**
	 * @var \Phalconmerce\Models\Popo\ConfigurableProduct
	 */
	public $configurableProduct;

	/**
	 * @return mixed
	 */
	public static function getConfigurableClassName() {
		return str_replace('Configured', 'Configurable', __CLASS__);
	}

	public function loadConfigurableProduct() {
		$fqcn = self::getConfigurableClassName();
		$this->configurableProduct = $fqcn::findFirst($this->getConfigurableProductId());
	}

	/**
	 * @return int
	 */
	public function getConfigurableProductId() {
		return $this->fk_configurable_product_id;
	}

	/**
	 * @return \Phalconmerce\Models\Popo\ConfigurableProduct
	 */
	public function getConfigurableProduct() {
		return $this->configurableProduct;
	}
}