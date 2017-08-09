<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Api\Controllers;

use Phalcon\Di;
use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\MetaData\Memory;
use Phalconmerce\Models\AbstractModel;
use Phalconmerce\Models\Utils;

class DefaultController extends Controller {

	const POPO_FQCN = '\Phalconmerce\Models\Popo\\';
	const FORM_FQCN = '\Backend\Forms\\';

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

	public function createAction($entity) {
		$errorList = array();
		$fqcn = $this->getFQCN($entity);
		$formClass = $this->getFormFQCN($entity);

		/** @var \Phalconmerce\Models\AbstractModel $object */
		$object = new $fqcn;

		/** @var \Backend\Forms\FormBase $form */
		$form = new $formClass;

		$data = $this->request->getPost();
		if (!$form->isValid($data, $object)) {
			foreach ($form->getMessages() as $message) {
				$errorList[] = array(
					'field' => $message->getField(),
					'error' => $message->getMessage()
				);
			}

			$this->sendJson(400, $errorList);
			return false;
		}

		if ($object->save() == false) {
			foreach ($object->getMessages() as $message) {
				$errorList[] = array(
					'field' => $message->getField(),
					'error' => $message->getMessage()
				);
			}

			$this->sendJson(400, $errorList);
			return false;
		}

		$form->clear();

		$this->sendCreated('./'.$object->id);
	}

	public function deleteAction($entity, $id) {
		if (is_string($entity) && strlen($entity) > 1 && is_numeric($id)) {
			$errorList = array();
			$fqcn = $this->getFQCN($entity);
			$formClass = $this->getFormFQCN($entity);

			/** @var \Phalconmerce\Models\AbstractModel $object */
			$object = $fqcn::findFirst($id);

			if (!$object) {
				$this->send404();
			}
			else {
				if (!$object->delete()) {
					$errorList = array();
					foreach ($object->getMessages() as $message) {
						print_r($message);
					}
					exit;
					return false;
				}
				else {
					$this->sendJson(200);
				}
			}
		}
		$this->send404();
		return false;
	}

	public function replaceAction($entity, $id) {
		$this->update('put', $entity, $id);
	}

	public function modifyAction($entity, $id) {
		$this->update('patch', $entity, $id);
	}

	private function update($action, $entity, $id) {
		if (is_string($entity) && strlen($entity) > 1 && is_numeric($id)) {
			$errorList = array();
			$fqcn = $this->getFQCN($entity);
			$formClass = $this->getFormFQCN($entity);

			/** @var \Phalconmerce\Models\AbstractModel $object */
			$object = $fqcn::findFirst($id);

			if ($object !== false) {
				/** @var \Backend\Forms\FormBase $form */
				$form = new $formClass;

				// Populate $data with current values if patch/modify
				$data = array();
				$putComplete = true;
				// Get properties
				$metadata = $object->getModelsMetaData();
				$properties = $metadata->getAttributes($object);
				//$mandatoryProperties = $metadata->getNotNullAttributes($object);
				$excludedProperties = array($metadata->getIdentityField($object), 'inserted');

				if (is_array($properties) && sizeof($properties) > 0) {
					foreach ($properties as $currentProperty) {
						if (array_key_exists($currentProperty, $_REQUEST)) {
							$data[$currentProperty] = $_REQUEST[$currentProperty];
						}
						else if (!in_array($currentProperty, $excludedProperties)) {
							if ($action == 'put') {
								$putComplete = false;
								$errorList[] = $currentProperty . ' missing';
							}
							$data[$currentProperty] = $object->$currentProperty;
						}
					}
				}

				// If PUT and not all properties sent
				if ($action == 'put' && !$putComplete) {
					$this->sendJson(400, $errorList);
					return false;
				}

				if (!$form->isValid($data, $object)) {
					foreach ($form->getMessages() as $message) {
						$errorList[] = array(
							'field' => $message->getField(),
							'error' => $message->getMessage()
						);
					}

					$this->sendJson(400, $errorList);
					return false;
				}

				if ($object->save() == false) {
					foreach ($object->getMessages() as $message) {
						print_r($message);
					}
					exit;

					$this->sendJson(400, $errorList);
					return false;
				}

				$form->clear();

				$this->sendJson(200, $object);
			}
		}
		$this->send404();
	}

	private function sendJson($httpResponseCode, $jsonData = null) {
		// Using HTTP Response object
		$response = $this->getResponse();
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
			case 400 :
				$response->setStatusCode(400, "Bad Request");
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
			$response->setHeader('Content-Type', 'application/json');
			$response->setJsonContent($jsonData);
		}
		$response->send();
		exit;
	}

	private function getFQCN($className) {
		return self::POPO_FQCN.$className;
	}

	private function getFormFQCN($className) {
		return self::FORM_FQCN.$className.'Form';
	}

	private function sendCreated($url) {
		if ($url != '') {
			// Using HTTP Response object
			$response = $this->getResponse();
			$response->setStatusCode(201, "Created");
			$response->setHeader('Location', $url);
			$response->send();
			exit;
		}
	}

	private function send404() {
		$response = $this->getResponse();
		$response->setStatusCode(404, "Not Found");
		$response->setContent('Not Found');
		$response->send();
	}

	private function getResponse() {
		$response = new Response();
		// TODO really handle cors
		$response->setHeader('Access-Control-Allow-Origin', '*');
		return $response;
	}
}