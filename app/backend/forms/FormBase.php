<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Forms;

use Phalcon\Forms\Element\Radio;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Select;
use \Phalcon\Forms\Element\Text;
use \Phalcon\Forms\Element\Hidden;
use \Phalcon\Validation\Validator\PresenceOf;
use Phalconmerce\Models\Utils;

class FormBase extends Form {
	/**
	 * @var string
	 */
	protected $popoClassName;

	/**
	 * @var \Phalcon\Forms\Element[]
	 */
	protected $elementsList;

	protected static $excludedProperties = array(
		'id',
		'inserted',
		'updated'
	);

	/**
	 * Initialize the form
	 * @param \Phalconmerce\Models\AbstractModel $entity
	 * @param array $options
	 */
	public function initialize($entity = null, $options = array()) {
		//$this->popoClassName = str_replace('Form', '',(new \ReflectionClass($this))->getShortName());
		$tmp = explode('\\', static::class);
		$this->popoClassName = end($tmp);

		if (substr($this->popoClassName, -4) == 'Form') {
			$this->popoClassName = substr($this->popoClassName, 0, -4);
		}

		if (!isset($options['edit'])) {
			$this->view->setVar('formTitle', 'Add');
		}
		else {
			$this->add(new Hidden("id"));
			$this->view->setVar('formTitle', 'Edit');
		}

		$fqcn = \Phalconmerce\Models\Popo\Popogenerator\PhpClass::POPO_NAMESPACE . '\\' . $this->popoClassName;
		$propertiesList = \Phalconmerce\Models\Popo\Popogenerator\PhpClass::getClassProperties($fqcn);
		$labelsObject = new Labels($this->popoClassName);
		//Utils::debug($propertiesList);exit;

		foreach ($propertiesList as $currentPropertyName => $currentPropertyReflect) {
			$item = null;
			// If property to be edited in form
			if (!empty($currentPropertyName) && !in_array($currentPropertyName, self::$excludedProperties)) {
				// If Column annotation
				if ($currentPropertyReflect->has('Column')) {
					// Get column annotations infos
					$columnCollection = $currentPropertyReflect->get('Column');
					if ($columnCollection->hasArgument('type')) {
						$type = $columnCollection->getArgument('type');
						$length = 0;
						if ($columnCollection->hasArgument('length')) {
							$length = $columnCollection->getArgument('length');
						}

						// TODO add maxlength attribute

						// If long string
						if ($type == 'string' && $length > 255 || $type == 'text') {
							$item = new TextArea($currentPropertyName);
							$item->setAttribute('class', 'form-control');
							$item->setFilters(array('string'));
						}
						// If short string
						else if ($type == 'string') {
							$item = new Text($currentPropertyName);
							$item->setAttribute('class', 'form-control');
							$item->setFilters(array('string'));
						}
						// If float
						else if ($type == 'float') {
							$item = new Text($currentPropertyName);
							$item->setAttribute('class', 'form-control');
							$item->setFilters(array('float'));
						}
						// If int
						else if ($type == 'integer') {
							// Status case
							if ($currentPropertyName == 'status') {
								$item = new Select(
									$currentPropertyName,
									[
										2 => 'Disabled',
										1 => 'Enabled'
									]
								);
								$item->setAttribute('class', 'form-control');
								$item->setFilters(array('int'));
							}
							else {
								$item = new Text($currentPropertyName);
								$item->setAttribute('class', 'form-control');
								$item->setFilters(array('int'));
							}
						}
						// If boolean
						else if ($type == 'boolean') {
							$item = new Select(
								$currentPropertyName,
								[
									2 => 'No',
									1 => 'Yes'
								]
							);
							$item->setAttribute('class', 'form-control');
							$item->setFilters(array('int'));
						}
						else {
							die ($type . '(' . $length . ')');
						}

						// Mandatory / Required
						if (!$columnCollection->hasArgument('nullable') || $columnCollection->getArgument('nullable') != 'false') {
							$item->addValidators(array(
								new PresenceOf(array(
									'message' => 'This field is required'
								))
							));
						}

						// Adding item to this form, if created
						if (isset($item)) {
							$item->setLabel($labelsObject->getLongLabelForProperty($currentPropertyName));
							$this->elementsList[$currentPropertyName] = $item;
						}
					}
				}
			}
		}
	}

	/**
	 * Add an element/item to elements' list
	 *
	 * @param $currentPropertyName
	 * @param $element
	 */
	protected function addElement($currentPropertyName, $element) {
		$this->elementsList[$currentPropertyName] = $element;
	}

	/**
	 * Add all elements/items to the Phalcon Form
	 *
	 * @return bool
	 */
	protected function addElementsToForm() {
		if (sizeof($this->elementsList) > 0) {
			foreach ($this->elementsList as $currentElement) {
				$this->add($currentElement);
			}
			return true;
		}
		return false;
	}
}