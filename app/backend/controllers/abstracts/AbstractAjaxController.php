<?php

namespace Backend\Controllers\Abstracts;

use Backend\Controllers\ControllerBase;
use Phalconmerce\Models\Utils;

abstract class AbstractAjaxController extends ControllerBase {

	public function indexAction() {
		$this->sendJson(404, array('error'=>'Content does not exists'));
	}

	/**
	 * method that returns (display as JSON) every resources in configured cloudinary account
	 */
	public function cloudinaryAction() {
		$api = \Phalcon\Di::getDefault()->get('cloudinary');
		/** @var \Cloudinary\Api\Response $result */
		$result = $api->resources(array("type" => "upload", 'max_results'=>100, 'prefix'=>'bernard-orcel/'));
		$jsonData = array();

		foreach ($result as $allResources) {
			if (is_array($allResources)) {
				foreach ($allResources as $currentResource) {
					$options =  array("width"=>60, "height"=>60, "crop"=>"limit");
					$currentResource['thumbnail'] = cloudinary_url_internal($currentResource['public_id'], $options);
					$jsonData[] = $currentResource;
				}
			}
		}

		$this->sendJson(200, $jsonData);
	}
}

