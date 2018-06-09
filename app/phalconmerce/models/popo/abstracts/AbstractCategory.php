<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Db\Column;
use Phalconmerce\Models\AbstractDesignedModel;
use Phalconmerce\Models\FilterData;
use Phalconmerce\Models\FilterInterface;
use Phalconmerce\Models\Utils;

abstract class AbstractCategory extends AbstractDesignedModel implements FilterInterface {

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
	public $fk_category_id;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $type;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @Translate
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="integer", length=4, nullable=true, default=999)
	 * @Index
	 * @var int
	 */
	public $position;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/** @var AbstractCategory[] */
	private $subCategoriesList;

	/**
	 * @return mixed
	 */
	public function getParent() {
		return \Phalconmerce\Models\Popo\Category::findFirst($this->getParentId());
	}

	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->fk_category_id;
	}

	/**
	 * @param int $id
	 * @param string $slug
	 * @return \Phalconmerce\Models\FilterData[]
	 */
	public static function getFilterDataList($id = 0, $slug='category') {
		$returnedArray = array();

		$results = static::find(array(
			'fk_category_id = :fk_category_id:',
			'bind' => array(
				'fk_category_id' => $id
			),
			'bindTypes' => array(
				Column::BIND_PARAM_INT
			)
		));
		if (!empty($results) && $results->count() > 0) {
			Utils::debug($results);
			/** @var AbstractCategory $currentCategory */
			foreach ($results as $currentCategory) {
				$filterData = new FilterData();
				$filterData->id = $currentCategory->id;
				$filterData->value = $currentCategory->name;
				$filterData->slug = $slug;
				$filterData->productIdList = array();

				/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractProduct $currentProduct */
				foreach ($currentCategory->getProduct() as $currentProduct) {
					$filterData->productIdList = array_merge($filterData->productIdList, $currentProduct->getProductIdsForFilters());
				}
				$returnedArray[$currentCategory->name] = $filterData;
			}
		}

		return $returnedArray;
	}

	/**
	 * @return AbstractCategory[]
	 */
	public function getSubCategoriesList() {
		return $this->subCategoriesList;
	}

	/**
	 * @param AbstractCategory[] $subCategoriesList
	 */
	public function setSubCategoriesList($subCategoriesList) {
		$this->subCategoriesList = $subCategoriesList;
	}
}