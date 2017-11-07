<?php

namespace Backend\Controllers\Abstracts;

use Backend\Controllers\ControllerBase;
use Phalconmerce\Models\Popo\Category;
use Phalconmerce\Models\Utils;

abstract class AbstractAjaxController extends ControllerBase {

	public function indexAction() {
		$this->sendJson(404, array('error'=>'Content does not exists'));
	}

	/**
	 * method that returns (display as JSON) every resources in configured cloudinary account
	 */
	public function cloudinaryGlobalAction() {
		$api = \Phalcon\Di::getDefault()->get('cloudinary');
		/** @var \Cloudinary\Api\Response $result */
		$result = $api->resources(array("type" => "upload", 'max_results'=>500, 'prefix'=>$this->config->cloudinary['global_folder']));

		$this->cloudinaryResponse($result);
	}

	/**
	 * method that returns (display as JSON) every resources in configured cloudinary account
	 */
	public function cloudinaryProductsAction() {
		$api = \Phalcon\Di::getDefault()->get('cloudinary');
		/** @var \Cloudinary\Api\Response $result */
		$result = $api->resources(array("type" => "upload", 'max_results'=>500, 'prefix'=>$this->config->cloudinary['products_folder']));

		$this->cloudinaryResponse($result);
	}

	/**
	 * @param \Cloudinary\Api\Response $result
	 */
	protected function cloudinaryResponse($result) {
		$jsonData = array();

		foreach ($result as $allResources) {
			if (is_array($allResources)) {
				foreach ($allResources as $currentResource) {
					$options =  array("width"=>60, "height"=>60, "crop"=>"limit");
					$currentResource['thumbnail'] = cloudinary_url_internal($currentResource['public_id'], $options);
					$currentResource['url'] = substr($currentResource['url'], 5);
					$jsonData[] = $currentResource;
				}
			}
		}

		$this->sendJson(200, $jsonData);
	}

	public function categoryJsTreeAction() {
		$selectedIds = $this->request->getPost("selectedIds", "trim", array());
		$options = $this->request->getPost("options", "trim", array());

		$results = Category::find(array('fk_category_id = 0'));
		$jsonData = array();

		if (!empty($results) && $results->count() > 0) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCategory $currentCategory */
			foreach ($results as $currentCategory) {
				$jsonData[] = array(
					'id' => $currentCategory->id,
					'text' => $currentCategory->name,
					'icon' => isset($options['icon']) ? $options['icon'] : '',
					'state' => array(
						'opened' => true,
						'selected' => in_array($currentCategory->id, $selectedIds)
					),
					'children' => $this->subCcategoryJsTree($currentCategory->id, $selectedIds, $options),
					'li_attr' => isset($options['li_attr']) ? $options['li_attr'] : array(),
					'a_attr' => isset($options['a_attr']) ? $options['a_attr'] : array(),
				);
			}
		}

		$this->sendJson(200, $jsonData);
	}

	protected function subCcategoryJsTree($id, $selectedIds=array(), $options=array()) {
		$results = Category::find(array('fk_category_id = '.$id));
		$jsonData = array();

		if (!empty($results) && $results->count() > 0) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCategory $currentCategory */
			foreach ($results as $currentCategory) {
				$jsonData[] = array(
					'id' => $currentCategory->id,
					'text' => $currentCategory->name,
					'icon' => isset($options['icon']) ? $options['icon'] : '',
					'state' => array(
						'opened' => true,
						'selected' => in_array($currentCategory->id, $selectedIds)
					),
					'children' => $this->subCcategoryJsTree($currentCategory->id, $selectedIds, $options),
					'li_attr' => isset($options['li_attr']) ? $options['li_attr'] : array(),
					'a_attr' => isset($options['a_attr']) ? $options['a_attr'] : array(),
				);
			}
		}

		return $jsonData;
	}
}

