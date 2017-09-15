<?php

namespace Phalconmerce\Models\Popo\Generators\Popo;

use Phalconmerce\Models\Utils;

class RelationshipManyToMany extends Relationship {
	/** @var string */
	protected $idPropertyName;
	/** @var string */
	protected $externalIdPropertyName;
	/** @var string */
	protected $manyToManyFQCN;

	function __construct($propertyName, $className, $idPropertyName, $externalIdPropertyName, $manyToManyFQCN, $externalPropertyName='', $externalFQCN='', $externalClassname='') {
		parent::__construct($propertyName, $className, $externalPropertyName, $externalFQCN, $externalClassname, self::TYPE_MANY_TO_MANY);
		$this->idPropertyName = $idPropertyName;
		$this->externalIdPropertyName = $externalIdPropertyName;
		$this->manyToManyFQCN = $manyToManyFQCN;
	}

	public function getPhalconMethodName() {
		return 'hasManyToMany';
	}

	/**
	 * @return string
	 */
	public function getPhpContent() {
		$phpContent = '';
		if (!empty($this->externalPropertyName) && !empty($this->externalFQCN)) {
			$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 2) . '$this->' . $this->getPhalconMethodName() . '(' . PHP_EOL;
			$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 3) . '\'' . $this->getIdPropertyName() . '\',' . PHP_EOL;
			$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 3) . '\'' . $this->getManyToManyFQCN() . '\',' . PHP_EOL;
			$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 3) . '\'' . $this->getPropertyName() . '\',' . PHP_EOL;
			$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 3) . '\'' . $this->getExternalPropertyName() . '\',' . PHP_EOL;
			$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 3) . '\'' . $this->getExternalFQCN() . '\',' . PHP_EOL;
			$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 3) . '\'' . $this->getExternalIdPropertyName() . '\',' . PHP_EOL;
			$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 3) . 'array(\'alias\' => \''.$this->getExternalClassName().'\')' . PHP_EOL;
			$phpContent .= str_repeat(PhpClass::TAB_CHARACTER, 2) . ');' . PHP_EOL;
		}

		return $phpContent;
	}

	/**
	 * @return Relationship
	 */
	public function generateHasManyRelationship() {
		$object = new Relationship(
			$this->getIdPropertyName(),
			$this->getClassName(),
			$this->getPropertyName(),
			$this->getManyToManyFQCN(),
			Utils::getClassnameFromFQCN($this->getManyToManyFQCN()),
			self::TYPE_1_TO_MANY
		);
		return $object;
	}

	/**
	 * @param array $propertiesList
	 * @return Relationship
	 */
	public static function __set_state($propertiesList) {
		return new Relationship(
			$propertiesList['propertyName'],
			$propertiesList['className'],
			$propertiesList['idPropertyName'],
			$propertiesList['externalIdPropertyName'],
			$propertiesList['manyToManyFQCN'],
			$propertiesList['externalPropertyName'],
			$propertiesList['externalFQCN'],
			$propertiesList['externalClassName']
		);
	}

	/**
	 * @return string
	 */
	public function getIdPropertyName() {
		return $this->idPropertyName;
	}

	/**
	 * @return string
	 */
	public function getExternalIdPropertyName() {
		return $this->externalIdPropertyName;
	}

	/**
	 * @return string
	 */
	public function getManyToManyFQCN() {
		return $this->manyToManyFQCN;
	}

	/**
	 * @param string $externalFQCN
	 */
	public function setExternalFQCN($externalFQCN) {
		$this->externalFQCN = $externalFQCN;
	}

	/**
	 * @param string $externalPropertyName
	 */
	public function setExternalPropertyName($externalPropertyName) {
		$this->externalPropertyName = $externalPropertyName;
	}

	/**
	 * @param string $externalClassName
	 */
	public function setExternalClassName($externalClassName) {
		$this->externalClassName = $externalClassName;
	}
}