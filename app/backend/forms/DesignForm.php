<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Forms;

use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalconmerce\Models\Popo\Url;
use Phalconmerce\Models\Design;
use Phalconmerce\Models\DesignParam;
use Phalconmerce\Models\Utils;

class DesignForm extends FormBase {
	public $wysiwygClassSelector;
	public function initialize($entity = null, $options = array()) {
		if (empty($this->wysiwygClassSelector)) {
			$this->wysiwygClassSelector = 'summernote';
		}

		/** @var \Phalconmerce\Models\AbstractDesignedModel $entity  */
		if (is_object($entity) && is_a($entity, '\Phalconmerce\Models\AbstractDesignedModel')) {
			// Adding hidden input
			$this->add(new Hidden("id"));

			// Get Design object
			$design = Design::loadFromFile($entity->designSlug);
			$this->view->setVar('design', $design);

			// Generate a form element to each design param
			foreach ($design->getParams() as $currentDesignParam) {
				$item = null;
				if ($currentDesignParam->getType() == DesignParam::TYPE_BOOLEAN) {
					$item = new Select(
						$currentDesignParam->getName(),
						[
							2 => $this->di->get('backendService')->t('No'),
							1 => $this->di->get('backendService')->t('Yes')
						]
					);
					$item->setAttribute('class', 'form-control');
					$item->setFilters(array($currentDesignParam->getFilter()));
				}
				else if ($currentDesignParam->getType() == DesignParam::TYPE_INT) {
					$item = new Text($currentDesignParam->getName());
					$item->setAttribute('class', 'form-control');
					$item->setFilters(array($currentDesignParam->getFilter()));
					$item->setAttribute('maxlength', 16);
				}
				else if ($currentDesignParam->getType() == DesignParam::TYPE_FLOAT) {
					$item = new Text($currentDesignParam->getName());
					$item->setAttribute('class', 'form-control');
					$item->setFilters(array($currentDesignParam->getFilter()));
					$item->setAttribute('maxlength', 16);
				}
				else if ($currentDesignParam->getType() == DesignParam::TYPE_STRING) {
					$item = new Text($currentDesignParam->getName());
					$item->setAttribute('class', 'form-control');
					$item->setFilters(array($currentDesignParam->getFilter()));
					$item->setAttribute('maxlength', 255);
				}
				else if ($currentDesignParam->getType() == DesignParam::TYPE_HTML) {
					/*$item = new Summernote($currentDesignParam->getName());
					$item->setFilters(array('string'));*/
					$item = new TextArea($currentDesignParam->getName());
					$item->setAttribute('class', 'form-control '.$this->wysiwygClassSelector);
					$item->setFilters(array($currentDesignParam->getFilter()));
				}
				else if ($currentDesignParam->getType() == DesignParam::TYPE_URL) {
					$selectValues = Url::fkSelect()->getValues();
					$selectValues[-1] = $this->di->get('backendService')->t('External link');
					$item = new Select(
						$currentDesignParam->getName(),
						$selectValues
					);
					$item->setAttribute('class', 'form-control urlSelect');
					$item->setFilters(array($currentDesignParam->getFilter()));

					$urlItem = new Text($currentDesignParam->getName().DesignParam::URL_EXTERNAL_SUFFIX);
					$urlItem->setAttribute('class', 'form-control');
					$urlItem->setFilters(array('url'));
					$urlItem->setAttribute('maxlength', 255);
				}
				else if ($currentDesignParam->getType() == DesignParam::TYPE_IMAGE) {
					$item = new Text($currentDesignParam->getName());
					$item->setAttribute('class', 'form-control');
					$item->setFilters(array($currentDesignParam->getFilter()));
					$item->setAttribute('maxlength', 255);
				}

				// Add to form
				if (isset($item)) {
					$item->setDefault(isset($entity->designData[$currentDesignParam->getName()]) ? $entity->designData[$currentDesignParam->getName()] : '');
					$item->setLabel($currentDesignParam->getName());
					$this->addElement($currentDesignParam->getName(), $item);
				}
				// URL item if needed
				if (isset($urlItem)) {
					$urlItem->setDefault(isset($entity->designData[$currentDesignParam->getName().DesignParam::URL_EXTERNAL_SUFFIX]) ? $entity->designData[$currentDesignParam->getName().DesignParam::URL_EXTERNAL_SUFFIX] : '');
					$urlItem->setLabel($currentDesignParam->getName().DesignParam::URL_EXTERNAL_SUFFIX);
					$this->addElement($currentDesignParam->getName().DesignParam::URL_EXTERNAL_SUFFIX, $urlItem);
					unset($urlItem);
				}
			}

			$this->addElementsToForm();
		}
	}
}