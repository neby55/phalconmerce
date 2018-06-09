<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Forms;

use Phalcon\Di;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Radio;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Message;
use Phalcon\Validation\Message\Group;
use Phalcon\Validation\Validator\PresenceOf;
use Phalconmerce\Forms\Element\DateTime;
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
			$this->view->setVar('formTitle', Di::getDefault()->get('backendService')->t('Add'));
		}
		else {
			$this->add(new Hidden("id"));
			$this->view->setVar('formTitle', Di::getDefault()->get('backendService')->t('Edit'));
		}

		$fqcn = \Phalconmerce\Models\Popo\Generators\Popo\PhpClass::POPO_NAMESPACE . '\\' . $this->popoClassName;
		$propertiesList = \Phalconmerce\Models\Popo\Generators\Popo\PhpClass::getClassProperties($fqcn);
		$labelsObject = new Labels($this->popoClassName);
		$helpBlocksObject = new HelpBlocks($this->popoClassName);
		$placeholdersObject = new Placeholders($this->popoClassName);
		$this->helpBlocksList = array();
		$currentReflectionClass = new \ReflectionClass($fqcn);
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
								$currentForeignClassName = Utils::getClassNameFromTableName($matches[1]);
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
									$item->setFilters(array('string', 'trim'));
									$item->setAttribute('maxlength', $length);
									$item->setMessages(new Group([new Message('Foreign Key select can\'t be generated. Please configure the static method "fkSelect" for this class')]));
								}
							}
							// If this property means foreign key but Slug FK
							else if (preg_match('/^fk_([a-zA-Z][a-zA-Z0-9_]*)_slug$/', $currentPropertyName, $matches)) {
									$currentForeignClassName = Utils::getClassNameFromTableName($matches[1]);
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
										$item->setFilters(array('string', 'trim'));
										$item->setAttribute('maxlength', $length);
										$item->setMessages(new Group([new Message('Foreign Key select can\'t be generated. Please configure the static method "fkSelect" for this class')]));
									}
								}
							// If its design slug
							else if ($currentPropertyName == 'designSlug') {
								$item = new Select(
									$currentPropertyName,
									$this->di->get('backendService')->getDesignsSelectOptions()
								);
								$item->setAttribute('class', 'form-control');
								$item->setFilters(array('string', 'trim'));
							}
							// If its design data
							else if ($currentPropertyName == 'designData') {
								// TODO
							}
							else {

								// TODO placeholders

								// If long string
								if ($type == 'string' && $length > 255 || $type == 'text') {
									$item = new TextArea($currentPropertyName);
									$item->setAttribute('class', 'form-control');
									$item->setFilters(array('html'));
								}
								// If HTML
								else if ($type == 'html') {
									$item = new Text($currentPropertyName);
									$item->setAttribute('class', 'form-control');
									$item->setFilters(array('html'));
									$item->setAttribute('maxlength', $length);
								}
								// If short string
								else if ($type == 'string') {
									if ($currentReflectionClass->hasMethod($currentPropertyName . 'SelectOptions')) {
										$item = new Select(
											$currentPropertyName,
											call_user_func(array($fqcn, $currentPropertyName . 'SelectOptions'))
										);
										$item->setAttribute('class', 'form-control');
										$item->setFilters(array('string', 'trim'));
									}
									else {
										$item = new Text($currentPropertyName);
										$item->setAttribute('class', 'form-control');
										$item->setFilters(array('string', 'trim'));
										$item->setAttribute('maxlength', $length);
									}
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
									if ($currentReflectionClass->hasMethod($currentPropertyName . 'SelectOptions')) {
										$item = new Select(
											$currentPropertyName,
											call_user_func(array($fqcn, $currentPropertyName . 'SelectOptions'))
										);
										$item->setAttribute('class', 'form-control');
										$item->setFilters(array('int'));
									}
									else if ($currentPropertyName == 'status') {
										$item = new Select(
											$currentPropertyName,
											[
												2 => $this->di->get('backendService')->t('Disabled'),
												1 => $this->di->get('backendService')->t('Enabled')
											]
										);
										$item->setAttribute('class', 'form-control');
										$item->setFilters(array('int'));
									}
									else {
										$item = new Text($currentPropertyName);
										$item->setAttribute('class', 'form-control');
										$item->setFilters(array('int'));
										if ($length > 0) {
											$item->setAttribute('maxlength', $length);
										}
									}
								}
								// If boolean
								else if ($type == 'boolean') {
									$item = new Select(
										$currentPropertyName,
										[
											2 => $this->di->get('backendService')->t('No'),
											1 => $this->di->get('backendService')->t('Yes')
										]
									);
									$item->setAttribute('class', 'form-control');
									$item->setFilters(array('int'));
								}
								// If timestamp
								else if ($type == 'timestamp') {
									$item = new DateTime($currentPropertyName);
									$item->setAttribute('class', 'form-control');
									$item->setAttribute('type', 'datetime');
									$item->setAttribute('maxlength', 19);
								}
								// If date
								else if ($type == 'date') {
									$item = new Date($currentPropertyName);
									$item->setAttribute('class', 'form-control');
									$item->setAttribute('type', 'date');
									$item->setAttribute('maxlength', 10);
								}
								// If datetime
								else if ($type == 'datetime') {
									$item = new DateTime($currentPropertyName);
									$item->setAttribute('class', 'form-control');
									$item->setAttribute('type', 'datetime');
									$item->setAttribute('maxlength', 19);
								}
								// If gps
								else if ($type == 'gps') {
									$item = new Text($currentPropertyName);
									$item->setAttribute('class', 'form-control');
									$item->setFilters(array('float'));
									$item->setAttribute('maxlength', \Phalconmerce\Models\Popo\Generators\Db\Table::GPS_SIZE + \Phalconmerce\Models\Popo\Generators\Db\Table::GPS_SCALE + 1);
								}
								else {
									throw new \Exception('FormBase class => unknown type : '.$type . '(' . $length . ')');
								}
							}

							// Mandatory / Required
							if (!$columnCollection->hasArgument('nullable') || $columnCollection->getArgument('nullable') === false) {
								$item->addValidators(array(
									new PresenceOf(array(
										'message' => $this->di->get('backendService')->t('This field is required')
									))
								));
							}

							// Adding item to this form, if created
							if (isset($item)) {
								$item->setLabel($labelsObject->getLongLabelForProperty($currentPropertyName));
								$this->addElement($currentPropertyName, $item);
								$this->addHelpBlock($currentPropertyName, $helpBlocksObject->getText($currentPropertyName));
								// If placeholder exists
								if ($placeholdersObject->propertyExists($currentPropertyName)) {
									$item->setAttribute('placeholder', $placeholdersObject->getText($currentPropertyName));
								}
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
		return is_array($this->helpBlocksList) && array_key_exists($propertyName, $this->helpBlocksList) && !empty($this->helpBlocksList[$propertyName]);
	}

	/**
	 * @param string $propertyName
	 * @return bool
	 */
	public function getHelpBlock($propertyName) {
		return $this->helpBlocksList[$propertyName];
	}
}