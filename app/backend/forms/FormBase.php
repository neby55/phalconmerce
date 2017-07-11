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

		$fqcn = \Phalconmerce\Models\Popo\Popogenerator\PhpClass::POPO_NAMESPACE . '\\'.$this->popoClassName;
		$propertiesList = \Phalconmerce\Models\Popo\Popogenerator\PhpClass::getClassProperties($fqcn);
		//Utils::debug($propertiesList);exit;

		foreach ($propertiesList as $currentPropertyName=>$currentPropertyReflect) {
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

						// If long string
						if ($type == 'string' && $length > 255 || $type == 'text') {
							$item = new TextArea($currentPropertyName);
							$item->setLabel($currentPropertyName); // TODO set label with translation or csv file
							$item->setAttribute('class', 'form-control');
							$item->setFilters(array('string'));
						}
						// If short string
						else if ($type == 'string') {
							$item = new Text($currentPropertyName);
							$item->setLabel($currentPropertyName); // TODO set label with translation or csv file
							$item->setAttribute('class', 'form-control');
							$item->setFilters(array('string'));
						}
						// If float
						else if ($type == 'float') {
							$item = new Text($currentPropertyName);
							$item->setLabel($currentPropertyName); // TODO set label with translation or csv file
							$item->setAttribute('class', 'form-control');
							$item->setFilters(array('float'));
						}
						// If int
						else if ($type == 'integer') {
							$item = new Text($currentPropertyName);
							$item->setLabel($currentPropertyName); // TODO set label with translation or csv file
							$item->setAttribute('class', 'form-control');
							$item->setFilters(array('int'));
						}
						// If boolean
						else if ($type == 'boolean') {
							$item = new Radio(
								$currentPropertyName,
								[
									1 => 'Yes',
									2 => 'No'
								]
							);
							// Todo handle a new class named RadioGroup
							$item->setLabel($currentPropertyName); // TODO set label with translation or csv file
							$item->setAttribute('class', 'form-control');
							$item->setFilters(array('int'));
						}
						else {
							die ($type.'('.$length.')');
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
							$this->add($item);
						}
					}
				}
			}
		}
	}
}