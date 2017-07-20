<?php

namespace Phalconmerce\Models\Popo\Generators\Popo;

class Relationship {
	/** @var string */
	protected $propertyName;
	/** @var string */
	protected $className;
	/** @var string */
	protected $externalPropertyName;
	/** @var string */
	protected $externalFQCN;
	/** @var int */
	protected $relationshipType;

	public static $relationshipsList = array(
		1 => '1:n',
		2 => 'n:1',
		3 => 'n:m',
		4 => '1:1',
	);

	const TYPE_1_TO_MANY = 1;
	const TYPE_MANY_TO_1 = 2;
	const TYPE_MANY_TO_MANY = 3;
	const TYPE_1_TO_1 = 4;

	const DATA_FILENAME = 'popo.relationships';

	function __construct($propertyName, $className, $externalPropertyName, $externalFQCN, $relationshipType) {
		$this->propertyName = $propertyName;
		$this->className = $className;
		$this->externalPropertyName = $externalPropertyName;
		$this->externalFQCN = $externalFQCN;
		$this->setRelationshipType($relationshipType);
	}

	/**
	 * @return string
	 */
	public function getPhalconMethodName() {
		if ($this->relationshipType == self::TYPE_1_TO_MANY) {
			return 'hasMany';
		}
		else if ($this->relationshipType == self::TYPE_MANY_TO_1) {
			return 'belongsTo';
		}
		else if ($this->relationshipType == self::TYPE_1_TO_1) {
			return 'belongsTo';
		}
		return '';
	}

	/**
	 * @return string
	 */
	public function getPhpContent() {
		$phpContent = str_repeat(PhpClass::TAB_CHARACTER, 2) . '$this->' . $this->getPhalconMethodName() . '(' . PHP_EOL;
		$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 3) . '\'' . $this->getPropertyName() . '\',' . PHP_EOL;
		$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 3) . '\'' . $this->getExternalFQCN() . '\',' . PHP_EOL;
		$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 3) . '\'' . $this->getExternalPropertyName() . '\'' . PHP_EOL;
		$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 2) . ');' . PHP_EOL;

		return $phpContent;
	}

	/**
	 * @param array $propertiesList
	 * @return Relationship
	 */
	public static function __set_state($propertiesList) {
		return new Relationship(
			$propertiesList['propertyName'],
			$propertiesList['className'],
			$propertiesList['externalPropertyName'],
			$propertiesList['externalFQCN'],
			$propertiesList['relationshipType']
		);
	}

	/**
	 * @return string
	 */
	public function getPropertyName() {
		return $this->propertyName;
	}

	/**
	 * @return string
	 */
	public function getClassName() {
		return $this->className;
	}

	/**
	 * @return string
	 */
	public function getExternalPropertyName() {
		return $this->externalPropertyName;
	}

	/**
	 * @return string
	 */
	public function getExternalFQCN() {
		return $this->externalFQCN;
	}

	/**
	 * @return int
	 */
	public function getRelationshipType() {
		return $this->relationshipType;
	}

	/**
	 * @return int
	 */
	public function getTextRelationshipType() {
		return self::$relationshipsList[$this->relationshipType];
	}

	/**
	 * @param int $relationshipType
	 */
	protected function setRelationshipType($relationshipType) {
		if (array_key_exists($relationshipType, self::$relationshipsList)) {
			$this->relationshipType = $relationshipType;
		}
		else {
			throw new \InvalidArgumentException('Relationship Type is incorrect (' . $relationshipType . ')');
		}
	}
}