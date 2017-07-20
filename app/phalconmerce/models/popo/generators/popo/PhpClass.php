<?php

namespace Phalconmerce\Models\Popo\Generators\Popo;

use Phalcon\Di;
use Phalconmerce\Models\Utils;

class PhpClass {
	/** @var string */
	protected $className;
	/** @var string */
	protected $extendedClassName;
	/** @var string */
	protected $tableName;
	/** @var Property[] */
	protected $propertiesList;
	/** @var \Phalcon\Annotations\Collection[] */
	protected $parentPropertiesList;
	/** @var Relationship[] */
	protected $relationshipsList;

	const TAB_CHARACTER = "\t";
	const POPO_NAMESPACE = 'Phalconmerce\\Models\\Popo';
	const POPO_ABSTRACT_NAMESPACE = 'Phalconmerce\\Models\\Popo\\Abstracts';

	public function __construct($className, $extendedClassName='', $tableName='') {
		$this->className = $className;
		$this->extendedClassName = $extendedClassName;
		$this->tableName = $tableName;
		$this->propertiesList = array();
		$this->isThereForeignKey = false;

		// Load properties of parent abstract class
		if (!empty($this->extendedClassName)) {
			$fqcn = self::POPO_ABSTRACT_NAMESPACE . '\\' . $this->extendedClassName;
			$this->parentPropertiesList = self::getClassProperties($fqcn);
		}

		// Load relationships for this Class
		$relationshipsList = Utils::loadData(Relationship::DATA_FILENAME);
		if (is_array($relationshipsList) && sizeof($relationshipsList) > 0) {
			if (array_key_exists(strtolower($this->className), $relationshipsList)) {
				$this->relationshipsList = $relationshipsList[strtolower($this->className)];
			}
		}
	}

	/**
	 * @return string
	 */
	public function getPhpContent() {
		$phpContent = '<?php'.PHP_EOL.PHP_EOL;
		$phpContent .= 'namespace '.self::POPO_NAMESPACE.';'.PHP_EOL.PHP_EOL;
		$phpContent .= 'use '.self::POPO_ABSTRACT_NAMESPACE.'\\%s;'.PHP_EOL.PHP_EOL;
		$phpContent .= 'class %s extends %s {'.PHP_EOL;
		if (is_array($this->propertiesList) && sizeof($this->propertiesList) > 0) {
			$phpContent .= self::TAB_CHARACTER.'/** Properties generated with Popo Cli Generator */'.PHP_EOL;
			foreach ($this->propertiesList as $currentProperty) {
				$phpContent .= $currentProperty->getPhpContent(self::TAB_CHARACTER);
			}
			$phpContent .= PHP_EOL;
		}
		$phpContent .= self::TAB_CHARACTER.'// Add here your own properties'.PHP_EOL;
		$phpContent .= self::TAB_CHARACTER.'// See the extended Class to know current herited properties'.PHP_EOL;
		$phpContent .= self::TAB_CHARACTER.'// To understand Annotations you must provide to your class, see https://docs.phalconphp.com/en/3.0.0/reference/models-metadata.html#annotations-strategy'.PHP_EOL;
		$phpContent .= self::TAB_CHARACTER.'// or take a look at abstract classes provided by Phalconmerce'.PHP_EOL;
		$phpContent .= PHP_EOL;
		$phpContent .= self::TAB_CHARACTER.'public function initialize() {'.PHP_EOL;
		$phpContent .= str_repeat(self::TAB_CHARACTER, 2).'parent::initialize();'.PHP_EOL.PHP_EOL;
		$phpContent .= str_repeat(self::TAB_CHARACTER, 2).'// You can add here instructions that will be executed by the framework, after construction'.PHP_EOL.PHP_EOL;
		$phpContent .= str_repeat(self::TAB_CHARACTER, 2).'// Set the DB table related to this class'.PHP_EOL;
		$phpContent .= str_repeat(self::TAB_CHARACTER, 2).'$this->setSource(\'%s\');'.PHP_EOL;
		$phpContent .= $this->getPhpInitializeFunctionExtra();

		// If ForeignKeys
		if (sizeof($this->relationshipsList)) {
			$phpContent .= PHP_EOL;
			$phpContent .= str_repeat(self::TAB_CHARACTER, 2).'// Following lines contains relationships with other models'.PHP_EOL;
			foreach ($this->relationshipsList as $currentRelationship) {
				$phpContent .= $currentRelationship->getPhpContent().PHP_EOL;
			}
		}

		$phpContent .= self::TAB_CHARACTER.'}'.PHP_EOL;
		$phpContent .= '}'.PHP_EOL;

		return sprintf(
			$phpContent,
			$this->extendedClassName,
			$this->className,
			$this->extendedClassName,
			$this->tableName
		);
	}

	/**
	 * @return string
	 */
	protected function getPhpInitializeFunctionExtra() {
		return '';
	}

	public function setExtendedClassNameFromCoreTypeResponse($coreProductType) { }

	/**
	 * @param string $content
	 * @return int
	 */
	public function save($content) {
		$currentNewClassFilename = self::getPopoDirectory().DIRECTORY_SEPARATOR.$this->className.'.php';
		return file_put_contents($currentNewClassFilename, $content);
	}

	public function initTableName($prefix='') {
		$this->tableName = $prefix.Utils::getTableNameFromClassName($this->className);
	}

	/**
	 * @return array
	 */
	public static function getAbstractClasses() {
		$abstractClassesList = array();
		if ($handle = opendir(self::getPopoDirectory().DIRECTORY_SEPARATOR.'abstracts')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..' && substr($entry, -4) == '.php') {
					$abstractClassName = substr($entry, 0, -4);
					// We cannot generate Product, there is another script to do that
					if (!in_array($abstractClassName, PhpProductClass::$abstractProductClassesList)) {
						$abstractClassesList[str_replace('Abstract', '', $abstractClassName)] = $abstractClassName;
					}
				}
			}
		}
		return $abstractClassesList;
	}

	/**
	 * @return string
	 */
	public static function getPopoDirectory() {
		return DI::getDefault()->get('configPhalconmerce')->popoModelsDir;
	}

	/**
	 * @param string $fullyQualifiedClassName
	 * @return bool|\Phalcon\Annotations\Collection[]
	 */
	public static function getClassProperties($fullyQualifiedClassName) {
		$reader = new \Phalcon\Annotations\Adapter\Memory();
		// Reflect the annotations in the class Example
		$reflector = $reader->get($fullyQualifiedClassName);

		return $reflector->getPropertiesAnnotations();
	}

	public function addProperty(Property $propertyObject) {
		$this->propertiesList[$propertyObject->getName()] = $propertyObject;
	}

	/**
	 * @return string
	 */
	public function getClassName() {
		return $this->className;
	}
}