<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Db\Column;
use Phalconmerce\Models\Popo\GroupedProductHasProduct;
use Phalconmerce\Models\Popo\Product;
use Phalconmerce\Models\Utils;

abstract class AbstractGroupedProduct extends AbstractFinalProduct {
	/**
	 * @var \Phalconmerce\Models\Popo\Product[]
	 */
	public $childrenProductList;

	public function loadRelatedProducts() {
		if ($this->id > 0) {
			// Assuming GroupedProduct has a relationship with product determined in inialize()
			$this->childrenProductList = array();
			$resultSet = $this->getProduct();
			if (!empty($resultSet) && $resultSet->count() > 0) {
				foreach ($resultSet as $currentObject) {
					$this->childrenProductList[] = $currentObject;
				}
			}
		}
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
				$groupedProductHasProduct = $fqcn::findFirst(
					array(
						'conditions' => 'fk_groupedproduct_id = :groupedProductId:
					        AND ' . 'fk_product_id = :productId:',
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