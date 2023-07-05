<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models;

use Phalcon\Di;
use Phalconmerce\Models\Popo\Generators\Popo\PhpClass;
use Phalconmerce\Models\Popo\Generators\Popo\Property;

class FkSelect {
	/** @var string */
	protected $valueField;
	/** @var string */
	protected $pattern;
	/** @var string */
	protected $fqcn;
	/** @var string[] */
	protected $labelFields;

	function __construct($valueField='', $pattern='', $fqcn='', $labelFields=array()) {
		$this->valueField = $valueField;
		$this->pattern = $pattern;
		$this->fqcn = $fqcn;
		$this->labelFields = $labelFields;
	}

	/**
	 * @param string $fullyQualifiedClassName
	 * @return bool|FkSelect
	 */
	public static function getFromClasseName($fullyQualifiedClassName) {
		// Check if class exists
		if (class_exists($fullyQualifiedClassName)) {
			$currentReflectionClass = new \ReflectionClass($fullyQualifiedClassName);

			if ($currentReflectionClass->hasMethod('fkSelect')) {
				return $fullyQualifiedClassName::fkSelect();
			}
			else {
				$properties = $currentReflectionClass->getProperties();
				if (sizeof($properties) >= 2) {
					return new FkSelect(
						$properties[0]->getName(),
						'%s',
						$fullyQualifiedClassName,
						array($properties[1]->getName())
					);
				}
			}
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function getValueField() {
		return $this->valueField;
	}

	/**
	 * @return string
	 */
	public function getPattern() {
		return $this->pattern;
	}

	/**
	 * @return string
	 */
	public function getFqcn() {
		return $this->fqcn;
	}

	/**
	 * @return \string[]
	 */
	public function getLabelFields() {
		return $this->labelFields;
	}

	/**
	 * @param array $filters
	 * @param bool $noChoose
	 * @return array
	 */
	public function getValues($filters=array(), $noChoose=false) {
		if (!$noChoose) {
			$values = array('' => '-');
		}
		$fqcn = $this->fqcn;

		// Setting up the cache for the query
		if (is_array($filters)) {
			$filters['cache'] = array(
				'key' => 'fkSelect-' . str_replace('\\', '-', $this->getFqcn()),
				'lifetime' => 3600,
			);
		}
		else if (is_string($filters)) {
			$filters = array(
				$filters,
				'cache' => array(
					'key' => 'fkSelect-' . str_replace('\\', '-', $this->getFqcn()),
					'lifetime' => 3600
				)
			);
		}

		// If method "getDropdownArray" exists
		if (method_exists($fqcn, 'getDropdownArray')) {
			$newValues = $fqcn::getDropdownArray();
			foreach ($newValues as $index=>$value) {
				$values[$index] = $value;
			}
		}
		else {
			$data = $fqcn::find($filters);

			foreach ($data as $currentObject) {
				$labelList = array();
				if (!is_array($this->labelFields)) {
					$this->labelFields = array($this->labelFields);
				}
				foreach ($this->labelFields as $currentLabelField) {
					// if FK field
					if (Property::isForeignKeyFromName($currentLabelField)) {
						$currentPropertyObject = new Property($currentLabelField);
						$fqcn = PhpClass::POPO_NAMESPACE . '\\' . $currentPropertyObject->getForeignKeyClassName();
						$fkSubSelect = self::getFromClasseName($fqcn);
						if ($fkSubSelect !== false) {
							$fkValues = $fkSubSelect->getValues();
							if (is_array($fkValues) && sizeof($fkValues) > 0 && array_key_exists($currentObject->$currentLabelField, $fkValues)) {
								$labelList[] = $fkValues[$currentObject->$currentLabelField];
							}
						}
						else {
							$labelList[] = $currentPropertyObject->getForeignKeyClassName() . '::' . $currentObject->$currentLabelField;
						}
					}
					// Specific pseudo-label for Url "entity"
					else if ($currentLabelField == 'entityName' && is_a($currentObject, PhpClass::POPO_NAMESPACE . '\\Url')) {
						$currentForeignObject = $currentObject->getEntityObject();
						if (!empty($currentForeignObject)) {
							if (isset($currentForeignObject->name)) {
								$labelList[] = $currentForeignObject->name;
							}
							else if (isset($currentForeignObject->title)) {
								$labelList[] = $currentForeignObject->title;
							}
							else if (isset($currentForeignObject->sku)) {
								$labelList[] = $currentForeignObject->sku;
							}
						}
					}
					else {
						$labelList[] = $currentObject->$currentLabelField;
					}
				}
				$idField = $this->valueField;
				$values[$currentObject->$idField] = vsprintf($this->pattern, $labelList);
			}
		}

		return $values;
	}
}