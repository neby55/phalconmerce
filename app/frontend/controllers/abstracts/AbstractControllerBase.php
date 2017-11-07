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
	 * @param \Phalconmerce\Models\AbstractDesignedModel $object
	 */
	protected function setupDesign($object) {
		if (is_a($object, '\Phalconmerce\Models\AbstractDesignedModel')) {
			// Get Design from object
			$design = Design::loadFromFile($object->designSlug);

			if ($design !== false) {
				// Set every data
				foreach ($design->getParams() as $currentParam) {
					if (empty($this->view->getVar($currentParam->getName()))) {
						if (is_array($object->designData) && array_key_exists($currentParam->getName(), $object->designData)) {
							if ($currentParam->getType() == DesignParam::TYPE_URL) {
								// URL external exception
								if ($object->designData[$currentParam->getName()] == -1) {
									$this->view->setVar($currentParam->getName(), $object->designData[$currentParam->getName().DesignParam::URL_EXTERNAL_SUFFIX]);
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
										$this->view->setVar($currentParam->getName(), $urlObject->getFullUrl());
									}
									else {
										$this->view->setVar($currentParam->getName(), '');
									}
								}
							}
							else {
								$this->view->setVar($currentParam->getName(), $object->designData[$currentParam->getName()]);
							}
						}
						else {
							$this->view->setVar($currentParam->getName(), '');
						}
					}
				}

				$this->view->pick($design->getViewPick());
			}
		}
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