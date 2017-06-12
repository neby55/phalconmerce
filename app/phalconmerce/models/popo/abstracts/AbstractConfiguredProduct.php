<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractConfiguredProduct extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_configurableproduct_id;

	/**
	 * @var \Phalconmerce\Models\Popo\ConfigurableProduct
	 */
	public $configurableProduct;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_product_id;

	/**
	 * @var \Phalconmerce\Models\Popo\Product
	 */
	public $product;

	private function loadProduct() {
		if ($this->getProductId() > 0) {
			$this->product = \Phalconmerce\Models\Popo\Product::findFirst($this->getProductId());
		}
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

	public function initialize() {
		parent::initialize();

		$this->loadProduct();
	}

	/**
	 * @return int
	 */
	public function getProductId() {
		return $this->fk_product_id;
	}

	/**
	 * @return \Phalconmerce\Models\Popo\Product
	 */
	public function getProduct() {
		return $this->product;
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

	/**
	 * @param array $data
	 * @param array $whiteList
	 * @return bool
	 */
	public function save($data = null, $whiteList = null) {
		// Force coreType to related product
		$this->product->coreType = AbstractProduct::PRODUCT_TYPE_CONFIGURED;

		// TODO check if $data passed to "product" save method will work or not
		$this->product->save($data, $whiteList);

		// If first save
		if ($this->fk_product_id <= 0) {
			$this->fk_product_id = $this->product->id;
			$data['fk_product_id'] = $this->fk_product_id;
		}

		return parent::save($data, $whiteList);
	}
}