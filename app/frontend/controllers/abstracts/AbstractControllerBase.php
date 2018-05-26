<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Frontend\Controllers\Abstracts;

use Phalcon\Mvc\Controller;
use Phalconmerce\Models\Design;
use Phalconmerce\Models\DesignParam;
use Phalconmerce\Models\Popo\Seo;
use Phalconmerce\Models\Popo\Url;
use Phalconmerce\Models\Utils;

class AbstractControllerBase extends Controller {
	public function initialize() {
		// Check if website is not active
		if (!$this->getDI()->get('frontendService')->isWebsiteActive() && $this->dispatcher->getControllerName() != 'errors' && $this->dispatcher->getActionName() != 'maintenance') {
			// Then redirect to maintenance page
			$this->dispatcher->forward(
				[
					"controller" => 'errors',
					"action" => "maintenance"
				]
			);
			return false;
		}

		$config = $this->getDI()->get('config');
		$this->view->setVar('config', $config);

		$this->tag->prependTitle($config->adminTitle.' | ');
		$this->setSubtitle('Page Name');

		// Disabling default validators requiring all fields to be filled
		\Phalcon\Mvc\Model::setup(array(
			'notNullValidations' => false
		));

		// Handling Currency and Lang forms
		if ($this->getDI()->get('translation')->handleLangAndCurrencyFormPost()) {
			return $this->redirection($this->request->getURI());
		}

		// If no meta data set
		if (empty($this->getDI()->get('frontendService')->getMetaTitle())) {
			$currentRoute = $this->router->getMatchedRoute();
			if (!empty($currentRoute)) {
				/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractSeo $seoObject */
				$seoObject = Seo::getCacheByRouteName($currentRoute->getName(), $this->getDI()->get('translation')->getLangId());
				if ($seoObject !== false) {
					// Defines META
					$this->getDI()->get('frontendService')->setMetaTitle($seoObject->metaTitle);
					$this->getDI()->get('frontendService')->setMetaDescription($seoObject->metaDescription);
					$this->getDI()->get('frontendService')->setMetaKeywords($seoObject->metaKeywords);
				}
				else {
					$seoObject = Seo::getCacheByRouteName('default', $this->getDI()->get('translation')->getLangId());
					if ($seoObject !== false) {
						// Defines META
						$this->getDI()->get('frontendService')->setMetaTitle($seoObject->metaTitle);
						$this->getDI()->get('frontendService')->setMetaDescription($seoObject->metaDescription);
						$this->getDI()->get('frontendService')->setMetaKeywords($seoObject->metaKeywords);
					}
				}
			}
		}
	}

	/**
	 * @param \Phalcon\Mvc\Dispatcher $dispatcher
	 */
	public function beforeExecuteRoute($dispatcher) {
		// To be sure every name is lowercase
		$dispatcher->setControllerName(strtolower($dispatcher->getControllerName()));
		$dispatcher->setActionName(strtolower($dispatcher->getActionName()));
	}

	public function setSubtitle($str) {
		$this->view->setVar('h1', $this->di->get('frontendService')->t($str));
	}

	/**
	 * @param string $url
	 * @return bool
	 */
	public function redirection($url) {
		$this->response->redirect($url);
		$this->view->disable();
		return true;
	}

	/**
	 * @param string $routeName
	 * @param array $params
	 * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
	 */
	public function redirectToRoute($routeName, $params=array()) {
		$this->view->disable();
		if (is_array($params) && sizeof($params) > 0) {
			$params['for'] = $routeName;
			if (array_key_exists('fragment', $params)) {
				return $this->response->redirect($this->url->get($params).'#'.$params['fragment']);
			}
			else {
				return $this->response->redirect($params);
			}
		}
		else {
			return $this->response->redirect(array(
				'for' => $routeName
			));
		}
	}

	/**
	 * @param \Phalconmerce\Models\AbstractDesignedModel $object
	 */
	protected function setupDesign($object) {
		if (is_a($object, '\Phalconmerce\Models\AbstractDesignedModel')) {
			// Get Design from object
			$design = Design::loadFromFile($object->designSlug);

			if ($design !== false) {
				$this->view->setVars($this->getViewVars($object, $design));

				$this->view->pick($design->getViewPick());
			}
		}
	}

	/**
	 * @param \Phalconmerce\Models\AbstractDesignedModel $object
	 * @return string|void
	 */
	protected function getDesignContent($object, $viewVars=array()) {
		if (is_a($object, '\Phalconmerce\Models\AbstractDesignedModel')) {
			// Get Design from object
			$design = Design::loadFromFile($object->designSlug);

			if ($design !== false) {
				return $this->view->getPartial($design->getViewPick(), array_merge($viewVars, $this->getViewVars($object, $design)));
			}
		}
	}

	/**
	 * @param \Phalconmerce\Models\AbstractDesignedModel $object
	 * @param \Phalconmerce\Models\Design $design
	 * @return array
	 */
	protected function getViewVars($object, $design) {
		$vars = array();

		// Set every data
		foreach ($design->getParams() as $currentParam) {
			if (empty($this->view->getVar($currentParam->getName()))) {
				if (is_array($object->designData) && array_key_exists($currentParam->getName(), $object->designData)) {
					if ($currentParam->getType() == DesignParam::TYPE_URL) {
						// URL external exception
						if ($object->designData[$currentParam->getName()] == -1) {
							$vars[$currentParam->getName()] = $object->designData[$currentParam->getName().DesignParam::URL_EXTERNAL_SUFFIX];
						}
						else {
							/** @var Url $urlObject */
							$urlObject = Url::findFirstById($object->designData[$currentParam->getName()]);
							if (is_object($urlObject)) {
								if ($this->di->get('translation')->getLangId() != $object->designData[$currentParam->getName()]) {
									$newUrlObject = $urlObject->getUrlForOtherLang($this->di->get('translation')->getLangId());
									if (is_object($newUrlObject)) {
										$urlObject = $newUrlObject;
									}
								}
								$vars[$currentParam->getName()] = $urlObject->getFullUrl();
							}
							else {
								$vars[$currentParam->getName()] = '';
							}
						}
					}
					else if ($currentParam->getType() == DesignParam::TYPE_ARRAY || $currentParam->getType() == DesignParam::TYPE_OBJECT) {
						// Call a special method which can send special data (array of products for example)
						$vars[$currentParam->getName()] = $object->getDesignDataArray($currentParam->getName());
					}
					else {
						$vars[$currentParam->getName()] = $object->designData[$currentParam->getName()];
					}
				}
				else {
					$vars[$currentParam->getName()] = '';
				}
			}
		}

		return $vars;
	}

	/**
	 * @param int $httpResponseCode
	 * @param mixed $jsonData
	 */
	protected function sendJson($httpResponseCode, $jsonData = array()) {
		// Using HTTP Response object
		$response = $this->response;
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

		$response->setHeader('Content-Type', 'application/json');
		$response->setJsonContent($jsonData);

		$response->send();
		exit;
	}
}