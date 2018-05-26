<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 03/02/2017
 * Time: 13:11
 */

namespace Phalconmerce\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;
use Phalconmerce\Models\Popo\Category;
use Phalconmerce\Models\Popo\Url;

class AbstractDesignedModel extends AbstractModel {
	/**
	 * Design slug
	 *
	 * @Column(type="string", length=32, nullable=true)
	 * @var string
	 */
	public $designSlug;

	/**
	 * Data used on Design (template)
	 * Gets from jsonDesignData which is stored in DB
	 * @var array
	 */
	public $designData;

	/**
	 * JSON representation of data for designs
	 *
	 * @Column(type="text", nullable=true, editable=false)
	 * @var string
	 */
	public $designJson;

	public function afterFetch() {
		$this->designData = json_decode($this->designJson, true);
	}

	/**
	 * @param Model\MetaDataInterface $metaData
	 * @param bool $exists
	 * @param mixed $identityField
	 * @return bool
	 */
	protected function _preSave(\Phalcon\Mvc\Model\MetaDataInterface $metaData, $exists, $identityField) {
		$this->designJson = json_encode($this->designData, JSON_UNESCAPED_UNICODE);

		return parent::_preSave($metaData, $exists, $identityField);
	}

	/**
	 * @return bool|string
	 */
	public function getBreadcrumbName() {
		if (isset($this->name)) {
			return $this->name;
		}
		if (isset($this->title)) {
			return $this->title;
		}
		return false;
	}

	public function setBreadCrumbsFromCategories() {
		$parentCategory = $this->getCategory();
		if (!empty($parentCategory)) {
			// If resultSet
			if (is_a($parentCategory, 'Phalcon\Mvc\Model\Resultset\Simple') && $parentCategory->count() > 0) {
				// TODO improve it with storing last category viewed in session
				$parentCategory = $parentCategory->getLast();
			}

			if (method_exists($parentCategory, 'setBreadCrumbsFromCategories')) {
				$parentCategory->setBreadCrumbsFromCategories();
			}
		}

		// If there is an id property
		if (isset($this->id) && method_exists($this, 'getBreadcrumbName')) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractUrl $urlObject */
			$urlObject = Url::getByEntity($this->getSource(), $this->id, Di::getDefault()->get('translation')->getLangId());
			if (!empty($urlObject)) {
				Di::getDefault()->get('frontendService')->addBreadcrumb(new Breadcrumb(
					$this->getBreadcrumbName(),
					$urlObject->id
				));
			}
		}
	}

	public function setBreadCrumbsFromMenus() {
		// Prepare array of breadcrumbs
		$breadcrumbsList = array();

		/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractUrl $urlObject */
		$urlObject = Url::getByEntity($this->getSource(), $this->id, Di::getDefault()->get('translation')->getLangId());
		if (!empty($urlObject)) {
			// Searching for Menu
			$results = $urlObject->getMenu();
			if (!empty($results) && $results->count() > 0) {
				/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractMenu $currentMenu */
				$currentMenu = $results->getFirst();

				while (!empty($currentMenu)) {
					// Adding to local array
					$breadcrumbsList[] = new Breadcrumb(
						$currentMenu->name,
						0 // no URL
					);
					// Move to its parent if exists
					$currentMenu = $currentMenu->getMenu();
				}
			}
		}

		// If breadcrumbs has been added locally, send them to frontendService
		if (sizeof($breadcrumbsList) > 0) {
			$breadcrumbsList = array_reverse($breadcrumbsList);
			foreach ($breadcrumbsList as $currentBreadcrumb) {
				Di::getDefault()->get('frontendService')->addBreadcrumb($currentBreadcrumb);
			}
		}
	}

	/**
	 * Method to be override on children (empty string is the default value)
	 * @return string
	 */
	public function getDesignDataArray($param) {
		return '';
	}
}