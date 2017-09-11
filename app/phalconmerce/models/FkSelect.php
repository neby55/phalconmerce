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
	 * @return array
	 */
	public function getValues($filters=array()) {
		$values = array('' => Di::getDefault()->get('backendService')->t('choose'));
		$fqcn = $this->fqcn;

		$data = $fqcn::find($filters);

		foreach ($data as $currentObject) {
			$labelList = array();
			if (!is_array($this->labelFields)) {
				$this->labelFields = array($this->labelFields);
			}
			foreach ($this->labelFields as $currentLabelField) {
				$labelList[] = $currentObject->$currentLabelField;
			}
			$idField = $this->valueField;
			$values[$currentObject->$idField] = vsprintf($this->pattern, $labelList);
		}

		return $values;
	}
}