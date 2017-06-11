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

	/**
	 * @var \Phalconmerce\Models\Popo\Product[]
	 */
	public $childrenProductList;

	private function loadProduct() {
		if ($this->getProductId() > 0) {
			$this->product = \Phalconmerce\Models\Popo\Product::findFirst($this->getProductId());
		}
	}

	private function loadChildrenProducts() {
		if ($this->id > 0) {
			$tmpObject = new \Phalconmerce\Models\Popo\Product();
			$this->childrenProductList = \Phalconmerce\Models\Popo\Product::find(
				array(
					'conditions' => $tmpObject->prefix . 'fk_groupedproduct_id = :groupedProductId:',
					'bind' => array(
						'groupedProductId' => $this->id
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
	 * @param \Phalconmerce\Models\Popo\Product $product
	 * @return mixed
	 */
	public function addProduct($product) {
		if ($this->id > 0) {
			if (is_a($product, 'AbstractProduct')) {
				$fqcn = __CLASS__ . 'HasProduct';
				$groupedProductHasProduct = new $fqcn();
				$groupedProductHasProduct->fk_groupedproduct_id = $this->id;
				$groupedProductHasProduct->fk_product_id = $product->id;
				return $groupedProductHasProduct->save();
			}
			else {
				throw new \InvalidArgumentException('product passed to method "addProduct" is not a child of AbstractProduct');
			}
		}
		else {
			throw new \InvalidArgumentException('groupedProduct has a null ID');
		}
	}

	/**
	 * @param \Phalconmerce\Models\Popo\Product $product
	 * @return mixed
	 */
	public function deleteProduct($product) {
		if ($this->id > 0) {
			if (is_a($product, 'AbstractProduct')) {
				$fqcn = __CLASS__ . 'HasProduct';
				$groupedProductHasProductTmp = new $fqcn();
				$groupedProductHasProduct = $fqcn::findFirst(
					array(
						'conditions' => $groupedProductHasProductTmp->prefix . 'fk_groupedproduct_id = :groupedProductId:
					        AND ' . $groupedProductHasProductTmp->prefix . 'fk_product_id = :productId:',
						'bind' => array(
							'groupedProductId' => $this->id,
							'productId' => $product->id
						),
						'bindTypes' => array(
							Column::BIND_PARAM_INT,
							Column::BIND_PARAM_INT
						)
					)
				);

				// If results
				if ($groupedProductHasProduct !== false) {
					return $groupedProductHasProduct->delete();
				}

				return false;
			}
			else {
				throw new \InvalidArgumentException('product passed to method "addProduct" is not a child of AbstractProduct');
			}
		}
		else {
			throw new \InvalidArgumentException('groupedProduct has a null ID');
		}
	}
}