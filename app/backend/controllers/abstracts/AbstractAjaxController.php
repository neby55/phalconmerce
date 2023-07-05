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
		//$result = $api->resources(array("type" => "upload", 'max_results'=>1000, 'prefix'=>$this->config->cloudinary['global_folder']));
		$result = $api->resources(array("type" => "upload", 'max_results'=>1000));

		$this->cloudinaryListResponse($result, $this->config->cloudinary['global_folder']);
	}

	/**
	 * method that returns (display as JSON) every resources in configured cloudinary account
	 */
	public function cloudinaryProductsAction() {
		$api = \Phalcon\Di::getDefault()->get('cloudinary');
		/** @var \Cloudinary\Api\Response $result */
		//$result = $api->resources(array("type" => "upload", 'max_results'=>1000, 'prefix'=>$this->config->cloudinary['products_folder']));
		$result = $api->resources(array("type" => "upload", 'max_results'=>1000));

		$this->cloudinaryListResponse($result, $this->config->cloudinary['products_folder']);
	}

	/**
	 * method that returns (display as JSON) every infos on a cloudinary resource
	 */
	public function cloudinaryResourceAction() {
		if ($this->request->isPost()) {
			$publicId = $this->request->getPost('public_id');
			$api = \Phalcon\Di::getDefault()->get('cloudinary');
			/** @var \Cloudinary\Api\Response $result */
			$result = $api->resource($publicId);

			$this->sendJson(200, $result);
		}
	}

	/**
	 * method that delete a tag on a given resource
	 */
	public function cloudinaryUpdateResourceTagAction() {
		if ($this->request->isPost()) {
			$publicId = $this->request->getPost('public_id');
			$tags = $this->request->getPost('tags');
			if (empty($tags) || !is_array($tags)) {
				$tags = array();
			}
			$api = \Phalcon\Di::getDefault()->get('cloudinary');
			/** @var \Cloudinary\Api\Response $result */
			$result = $api->update(
				$publicId,
				array(
					'tags' => join(',', $tags)
				)
			);

			$this->sendJson(200, $result);
		}
	}

	/**
	 * @param \Cloudinary\Api\Response $result
	 * @param string $filter
	 */
	protected function cloudinaryListResponse($result, $filter='') {
		$jsonData = array();

		foreach ($result as $allResources) {
			if (is_array($allResources)) {
				foreach ($allResources as $currentResource) {
					if (empty($filter) || substr($currentResource['public_id'], 0, strlen($filter)) == $filter) {
						$options = array("width" => 200, "height" => 200, "crop" => "limit");
						$currentResource['thumbnail'] = cloudinary_url_internal($currentResource['public_id'], $options);
						$currentResource['url'] = substr($currentResource['url'], 5);
						$jsonData[] = $currentResource;
					}
				}
			}
		}

		$this->sendJson(200, $jsonData);
	}

	public function categoryJsTreeAction() {
		$selectedIds = $this->request->getPost("selectedIds", "trim", array());
		$options = isset($_POST['option']) ? $_POST['option'] : array();

		$results = Category::find(array(
			'fk_category_id = 0',
			'order' => 'position'
		));
		$jsonData = array();

		if (!empty($results) && $results->count() > 0) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCategory $currentCategory */
			foreach ($results as $currentCategory) {
				// Add data-id attr to each <a>
				$options['a_attr']['data-id'] = $currentCategory->id;
				$options['a_attr']['data-type'] = $currentCategory->type;

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
		$results = Category::find(array(
			'fk_category_id = '.$id,
			'order' => 'position'
		));
		$jsonData = array();

		if (!empty($results) && $results->count() > 0) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCategory $currentCategory */
			foreach ($results as $currentCategory) {
				// Add data-id attr to each <a>
				$options['a_attr']['data-id'] = $currentCategory->id;
				$options['a_attr']['data-type'] = $currentCategory->type;

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

