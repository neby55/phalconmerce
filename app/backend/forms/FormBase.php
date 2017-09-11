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
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Message;
use Phalcon\Validation\Message\Group;
use Phalcon\Validation\Validator\PresenceOf;
use Phalconmerce\Models\FkSelect;
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

	/**
	 * @var \Phalcon\Forms\Element[]
	 */
	protected $helpBlocksList;

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

		$fqcn = \Phalconmerce\Models\Popo\Generators\Popo\PhpClass::POPO_NAMESPACE . '\\' . $this->popoClassName;
		$propertiesList = \Phalconmerce\Models\Popo\Generators\Popo\PhpClass::getClassProperties($fqcn);
		$labelsObject = new Labels($this->popoClassName);
		$helpBlocksObject = new HelpBlocks($this->popoClassName);
		$this->helpBlocksList = array();
		//Utils::debug($propertiesList);exit;

		foreach ($propertiesList as $currentPropertyName => $currentPropertyReflect) {
			$item = null;
			// If property to be edited in form
			if (!empty($currentPropertyName) && !in_array($currentPropertyName, self::$excludedProperties)) {
				// If Column annotation
				if ($currentPropertyReflect->has('Column')) {
					// Get column annotations infos
					$columnCollection = $currentPropertyReflect->get('Column');
					// If field is editable in backend
					if (!$columnCollection->hasArgument('editable') || $columnCollection->getArgument('editable') !== false) {
						if ($columnCollection->hasArgument('type')) {
							$type = $columnCollection->getArgument('type');
							$length = 0;
							if ($columnCollection->hasArgument('length')) {
								$length = $columnCollection->getArgument('length');
							}


							// If this property means foreign key
							if (preg_match('/^fk_([a-zA-Z][a-zA-Z0-9_]*)_id$/', $currentPropertyName, $matches)) {
								$currentForeignClassName = $matches[1];
								$filterType = $type == 'string' ? 'string' : 'int';
								$fkFcqn = \Phalconmerce\Models\Popo\Generators\Popo\PhpClass::POPO_NAMESPACE . '\\' . $currentForeignClassName;
								$fkSelect = FkSelect::getFromClasseName($fkFcqn);

								if ($fkSelect !== false) {
									$item = new Select(
										$currentPropertyName,
										$fkSelect->getValues()
									);
									$item->setAttribute('class', 'form-control');
									$item->setFilters(array($filterType));
								}
								else {
									$item = new Text($currentPropertyName);
									$item->setAttribute('class', 'form-control');
									$item->setFilters(array('string'));
									$item->setAttribute('maxlength', $length);
									$item->setMessages(new Group([new Message('Foreign Key select can\'t be generated. Please configure the static method "fkSelect" for this class')]));
								}
							}
							else {

								// TODO placeholders

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
									$item->setAttribute('maxlength', $length);
								}
								// If float
								else if ($type == 'float') {
									$item = new Text($currentPropertyName);
									$item->setAttribute('class', 'form-control');
									$item->setFilters(array('float'));
									$item->setAttribute('maxlength', \Phalconmerce\Models\Popo\Generators\Db\Table::DECIMAL_SIZE + \Phalconmerce\Models\Popo\Generators\Db\Table::DECIMAL_SCALE + 1);
								}
								// If int
								else if ($type == 'integer' || $type == 'int') {
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
										$currentReflectionClass = new \ReflectionClass($fqcn);
										if ($currentReflectionClass->hasMethod($currentPropertyName . 'SelectOptions')) {
											$item = new Select(
												$currentPropertyName,
												call_user_func(array($fqcn, $currentPropertyName . 'SelectOptions'))
											);
											$item->setAttribute('class', 'form-control');
											$item->setFilters(array('int'));
										}
										else {
											$item = new Text($currentPropertyName);
											$item->setAttribute('class', 'form-control');
											$item->setFilters(array('int'));
											$item->setAttribute('maxlength', $length);
										}
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
								// If timestamp
								else if ($type == 'timestamp') {
									$item = new Text($currentPropertyName);
									$item->setAttribute('class', 'form-control');
									$item->setAttribute('type', 'date');
									$item->setAttribute('maxlength', 19);
								}
								else {
									die ($type . '(' . $length . ')');
								}
							}

							// Mandatory / Required
							if (!$columnCollection->hasArgument('nullable') || $columnCollection->getArgument('nullable') === false) {
								$item->addValidators(array(
									new PresenceOf(array(
										'message' => 'This field is required'
									))
								));
							}

							// Adding item to this form, if created
							if (isset($item)) {
								$item->setLabel($labelsObject->getLongLabelForProperty($currentPropertyName));
								$this->addElement($currentPropertyName, $item);
								$this->addHelpBlock($currentPropertyName, $helpBlocksObject->getText($currentPropertyName));
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Add an element/item to elements' list
	 *
	 * @param string $currentPropertyName
	 * @param \Phalcon\Forms\Element$element
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

	/**
	 * Add an help-block's text to form's list
	 *
	 * @param string $currentPropertyName
	 * @param string $text
	 */
	protected function addHelpBlock($currentPropertyName, $text) {
		$this->helpBlocksList[$currentPropertyName] = $text;
	}

	/**
	 * @param string $propertyName
	 * @return bool
	 */
	public function hasHelpBlock($propertyName) {
		return array_key_exists($propertyName, $this->helpBlocksList);
	}

	/**
	 * @param string $propertyName
	 * @return bool
	 */
	public function getHelpBlock($propertyName) {
		return $this->helpBlocksList[$propertyName];
	}
}