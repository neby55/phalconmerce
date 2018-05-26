<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\AbstractModel;
use Phalconmerce\Models\Popo\Generators\Popo\PhpClass;
use Phalconmerce\Models\Utils;

abstract class AbstractFilter extends AbstractModel {

	const TYPE_UNIQUE = 1;
	const TYPE_MULTIPLE = 2;
	const TYPE_RANGE = 3;

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @Translate
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="string", length=16, nullable=false)
	 * @Index
	 * @var string
	 */
	public $slug;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @Index
	 * @var int
	 */
	public $type;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/** @var mixed */
	public $selection;

	/** @var \Phalconmerce\Models\FilterData[] */
	public $filterDataList;

	/**
	 * You should define in this static property authorized slug
	 * and the corresponding popo/entity which implements FilterInterface
	 * (write the FQCN)
	 *
	 * @return array
	 */
	protected static function getSlugList() {
		return array(
			'category' => array(
				'fqcn' => '\\'.PhpClass::POPO_NAMESPACE . '\\Category',
			),
			/* Example with attributes "Color" & Size
			'colors' => array(
				'fqcn' => '\\'.PhpClass::POPO_NAMESPACE . '\\Atrtibute',
				'force_id' => 1
			),
			'size' => array(
				'fqcn' => '\\'.PhpClass::POPO_NAMESPACE . '\\Atrtibute',
				'force_id' => 2
			)
			*/
		);
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public function loadFilterData($id = 0) {
		$this->filterDataList = array();

		$slugList = static::getSlugList();
		if (array_key_exists($this->slug, $slugList)) {
			$fqcn = $slugList[$this->slug]['fqcn'];
			if (is_subclass_of($fqcn, '\Phalconmerce\Models\FilterInterface')) {
				if (!empty($slugList[$this->slug]['force_id'])) {
					$id = $slugList[$this->slug]['force_id'];
				}
				// Get from cache
				$cacheKey = 'filter-data-'.$this->slug.'-'.$id;
				$data = Utils::loadCacheData($cacheKey);
				if (!empty($data)) {
					$this->filterDataList = $data;
				}
				else {
					$this->filterDataList = $fqcn::getFilterDataList($id, $this->slug);
					ksort($this->filterDataList);
					Utils::saveCacheData($this->filterDataList, $cacheKey);
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * @param AbstractProduct[] $productsList
	 * @return AbstractProduct[]
	 */
	public function filterProductsArray($productsList) {
		// TODO improve it
		$newProductsList = array();
		if (is_array($productsList)) {
			foreach ($productsList as $currentIndex=>$currentProduct) {
				if (is_a($currentProduct, '\Phalconmerce\Models\Popo\Abstracts\AbstractProduct')) {
					$canAdd = false;
					/** @var \Phalconmerce\Models\FilterData $currentFilterData */
					foreach ($this->filterDataList as $currentFilterData) {
						if ($this->selection == $currentFilterData->getFormValue()) {
							if (in_array($currentProduct->id, $currentFilterData->productIdList)) {
								Di::getDefault()->get('logger')->debug('add ok cause id=' . $currentProduct->id . ' in=(' . join(',', $currentFilterData->productIdList) . ') slug=' . $currentFilterData->slug . ' value=' . $currentFilterData->getFormValue());
								$canAdd = true;
								break;
							}
						}
					}
					if ($canAdd) {
						$newProductsList[$currentIndex] = $currentProduct;
					}
				}

			}
		}
		return $newProductsList;
	}
}