<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractFinalProduct extends AbstractModel {

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

	private function loadProduct() {
		if ($this->getRelatedProductId() > 0) {
			$this->product = \Phalconmerce\Models\Popo\Product::findFirst($this->getRelatedProductId());
		}
	}

	/**
	 * @return int
	 */
	public function getRelatedProductId() {
		return $this->fk_product_id;
	}

	/**
	 * @return \Phalconmerce\Models\Popo\Abstracts\AbstractProduct
	 */
	public function getRelatedroduct() {
		return $this->product;
	}

	/**
	 * @param array $data
	 * @param array $whiteList
	 * @return bool
	 */
	/*public function save($data = null, $whiteList = null) {
		echo get_class($this).'<br>';
		// Force coreType to related product
		$this->product->coreType = AbstractProduct::PRODUCT_TYPE_SIMPLE;

		// TODO check if $data passed to "product" save method will work or not
		$this->product->save($data, $whiteList);

		// If first save
		if ($this->fk_product_id <= 0) {
			$this->fk_product_id = $this->product->id;
			$data['fk_product_id'] = $this->fk_product_id;
		}

		return parent::save($data, $whiteList);
	}*/
}