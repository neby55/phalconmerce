<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Api\Controllers;

use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;
use Phalconmerce\Models\AbstractModel;
use Phalconmerce\Models\Utils;

class DefaultController extends Controller {
	public function indexAction($params = array()) {
		echo '<h1>index</h1>';
		Utils::debug($params);
	}

	public function listAction($entity) {
		if (is_string($entity) && strlen($entity) > 1) {
			$fqcn = $this->getFQCN($entity);

			$objectsList = $fqcn::find();

			$this->sendJson(200, $objectsList);
		}
		$this->send404();
	}

	public function readAction($entity, $id) {
		if (is_string($entity) && strlen($entity) > 1 && is_numeric($id)) {
			$fqcn = $this->getFQCN($entity);

			$object = $fqcn::findFirst($id);

			if ($object) {
				$this->sendJson(200, $object);
			}
		}
		$this->send404();
	}

	public function createAction($params = array()) {
		echo '<h1>create</h1>';
		Utils::debug($params);
	}

	private function sendJson($httpResponseCode, $jsonData = array()) {
		// Using HTTP Response object
		$response = new Response();
		// Change the HTTP status
		switch ($httpResponseCode) {
			case 200 :
				$response->setStatusCode(200, "OK");
				break;
			case 201 :
				$response->setStatusCode(201, "Created");
				break;
			case 204 :
				$response->setStatusCode(204, "No Content");
				break;
			case 404 :
				$response->setStatusCode(404, "Not Found");
				break;
			case 409 :
				$response->setStatusCode(409, "Conflict");
				break;
			default :
				$response->setStatusCode(405, "Method Not Allowed");
		}

		if ($jsonData) {
			$response->setJsonContent($jsonData);
		}
		$response->send();
		exit;
	}

	private function getFQCN($className) {
		return AbstractModel::POPO_FQCN.$className;
	}

	private function sendCreated($url) {
		if ($url != '') {
			// Using HTTP Response object
			$response = new Response();
			$response->setStatusCode(201, "Created");
			$response->redirect($url, true, 201);
		}
	}

	private function send404() {
		$response = new Response();
		$response->setStatusCode(404, "Not Found");
		$response->setContent('Not Found');
		$response->send();
	}
}