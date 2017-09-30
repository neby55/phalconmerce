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
use Phalconmerce\Models\Popo\Url;

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
	}

	public function setSubtitle($str) {
		$this->view->setVar('h1', $this->di->get('frontendService')->t($str));
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
					if (is_array($object->designData) && array_key_exists($currentParam->getName(), $object->designData)) {
						if ($currentParam->getType() == DesignParam::TYPE_URL) {
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
						else {
							$this->view->setVar($currentParam->getName(), $object->designData[$currentParam->getName()]);
						}
					}
					else {
						$this->view->setVar($currentParam->getName(), '');
					}
				}

				$this->view->pick($design->getViewPick());
			}
		}
	}
}