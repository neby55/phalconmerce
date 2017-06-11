<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Db\Column;
use Phalconmerce\Models\AbstractModel;

abstract class AbstractConfigurableProduct extends AbstractModel {

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
	public $fk_product_id;

	/**
	 * @var \Phalconmerce\Models\Popo\Product
	 */
	public $product;

	/**
	 * @var \Phalconmerce\Models\Popo\Abstracts\AbstractConfiguredProduct[]
	 */
	public $configuredProductList;

	private function loadProduct() {
		if ($this->getProductId() > 0) {
			$this->product = \Phalconmerce\Models\Popo\Product::findFirst($this->getProductId());
		}
	}

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

		$this->loadProduct();
		$this->loadConfiguredProducts();
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
	 * @param array $data
	 * @param array $whiteList
	 * @return bool
	 */
	public function save($data = null, $whiteList = null) {
		// Force coreType to related product
		$this->product->coreType = AbstractProduct::PRODUCT_TYPE_CONFIGURABLE;

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