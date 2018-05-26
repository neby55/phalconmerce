<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Services\Abstracts;

use Phalconmerce\Models\Popo\Category;
use Phalconmerce\Models\Design;

abstract class MainService implements \Phalcon\Di\InjectionAwareInterface {

	protected $_dependencyInjector;

	/** @var Design[] */
	protected $designsList;

	/**
	 * Sets the dependency injector
	 *
	 * @param mixed $dependencyInjector
	 */
	public function setDI(\Phalcon\DiInterface $dependencyInjector) {
		$this->_dependencyInjector = $dependencyInjector;
	}

	/**
	 * Returns the internal dependency injector
	 *
	 * @return \Phalcon\DiInterface
	 */
	public function getDI() {
		return $this->_dependencyInjector;
	}

	public function loadDesignsIfNeeded() {
		if (!isset($this->designsList) || !is_array($this->designsList)) {
			$this->designsList = Design::loadAllDesigns();;
		}
	}

	/**
	 * @return string[]
	 */
	public function getDesignsSelectOptions() {
		$this->loadDesignsIfNeeded();

		$options = array('' => '-');

		if (is_array($this->designsList) && sizeof($this->designsList)) {
			foreach ($this->designsList as $currentDesign) {
				$options[$currentDesign->getSlug()] = $currentDesign->getName();
			}
		}

		return $options;
	}

	/**
	 * @return Design
	 */
	public function getDesign($slug) {
		$this->loadDesignsIfNeeded();

		if (is_array($this->designsList) && sizeof($this->designsList) && array_key_exists($slug, $this->designsList)) {
			return $this->designsList[$slug];
		}
	}

	/**
	 * @param int $parentCategoryId
	 * @return \Phalconmerce\Models\Popo\Abstracts\AbstractCategory[]
	 */
	public function getCategoriesTree($parentCategoryId=0) {
		$results = Category::find(array(
			'fk_category_id = '.intval($parentCategoryId),
			'order' => 'position'
		));
		$resultsArray = array();

		if (!empty($results) && $results->count() > 0) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCategory $currentCategory */
			foreach ($results as $currentCategory) {
				// Get subcategories if exists
				$subCategories = $this->getCategoriesTree($currentCategory->id);
				if (!empty($subCategories) && is_array($subCategories)) {
					$currentCategory->setSubCategoriesList($subCategories);
				}
				// Add current category to the array
				$resultsArray[$currentCategory->id] = $currentCategory;
			}
		}

		return $resultsArray;
	}
}