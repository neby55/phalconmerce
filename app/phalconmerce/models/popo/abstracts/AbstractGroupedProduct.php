<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Db\Column;
use Phalconmerce\Models\AbstractModel;

abstract class AbstractGroupedProduct extends AbstractModel {

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
		$this->product = \Phalconmerce\Models\Popo\Product::findFirst($this->getProductId());
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
	 * @param array $data
	 * @param array $whiteList
	 * @return bool
	 */
	public function save($data = null, $whiteList = null) {
		// Force coreType to related product
		$this->product->coreType = AbstractProduct::PRODUCT_TYPE_GROUPED;

		// TODO check if $data passed to "product" save method will work or not
		$this->product->save($data, $whiteList);

		// If first save
		if ($this->fk_product_id <= 0) {
			$this->fk_product_id = $this->product->id;
			$data['fk_product_id'] = $this->fk_product_id;
		}

		return parent::save($data, $whiteList);
	}

	/**
	 * @param \Phalconmerce\Models\Popo\SimpleProduct $product
	 * @return mixed
	 */
	public function addProduct($product) {
		if (is_a($product, 'AbstractSimpleProduct')) {
			$groupedProductHasSimpleProduct = new \Phalconmerce\Models\Popo\GroupedProductHasSimpleProduct();
			$groupedProductHasSimpleProduct->fk_groupedproduct_id = $this->id;
			$groupedProductHasSimpleProduct->fk_simpleproduct_id = $product->id;
			return $groupedProductHasSimpleProduct->save();
		}
		else {
			throw new \InvalidArgumentException('product passed to method "addProduct" is not a child of AbstractSimpleProduct');
		}
	}

	/**
	 * @param \Phalconmerce\Models\Popo\SimpleProduct $product
	 * @return mixed
	 */
	public function deleteProduct($product) {
		if (is_a($product, 'AbstractSimpleProduct')) {
			$groupedProductHasSimpleProductTmp = new \Phalconmerce\Models\Popo\GroupedProductHasSimpleProduct();
			$groupedProductHasSimpleProduct = \Phalconmerce\Models\Popo\GroupedProductHasSimpleProduct::findFirst(
				array(
					'conditions' => $groupedProductHasSimpleProductTmp->prefix.'fk_groupedproduct_id = :groupedProductId:
					        AND '.$groupedProductHasSimpleProductTmp->prefix.'fk_simpleproduct_id = :simpleProductId:',
					'bind' => array(
						'groupedProductId' => $this->id,
						'simpleProductId' => $product->id
					),
					'bindTypes' => array(
						Column::BIND_PARAM_INT,
						Column::BIND_PARAM_INT
					)
				)
			);

			// If results
			if ($groupedProductHasSimpleProduct !== false) {
				return $groupedProductHasSimpleProduct->delete();
			}

			return false;
		}
		else {
			throw new \InvalidArgumentException('product passed to method "addProduct" is not a child of AbstractSimpleProduct');
		}
	}
}