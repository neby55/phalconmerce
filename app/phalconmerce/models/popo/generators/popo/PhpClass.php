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
		/* -- PROPERTIES -- */
		$propertiesContent = '';
		if (is_array($this->propertiesList) && sizeof($this->propertiesList) > 0) {
			$propertiesContent .= self::TAB_CHARACTER.'/** Properties generated with Popo Cli Generator */'.PHP_EOL;
			foreach ($this->propertiesList as $currentProperty) {
				$propertiesContent .= $currentProperty->getPhpContent(self::TAB_CHARACTER);
			}
			$propertiesContent .= PHP_EOL;
		}

		/* -- FOREIGN KEYS -- */
		$foreignKeysContent = '';
		if (sizeof($this->relationshipsList)) {
			$foreignKeysContent .= PHP_EOL;
			$foreignKeysContent .= str_repeat(self::TAB_CHARACTER, 2).'// Following lines contains relationships with other models'.PHP_EOL;
			foreach ($this->relationshipsList as $currentRelationship) {
				$foreignKeysContent .= $currentRelationship->getPhpContent().PHP_EOL;
			}
		}

		$phpContent = <<<'EOT'
<?php

namespace ##NAMESPACE##;

use Phalconmerce\Models\FkSelect;
use Phalconmerce\Services\BackendService;
use ##ABSTRACT_NAMESPACE##\##ABSTRACT_CLASSNAME##;

// Remove @Api annotations to disable acces to this class from API service
/**
 * @Api
 */

class ##CLASSNAME## extends ##ABSTRACT_CLASSNAME## {
	##PROPERTIES##
	// Add here your own properties
	// See the extended Class to know current herited properties
	// To understand Annotations you must provide to your class, see https://docs.phalconphp.com/en/3.0.0/reference/models-metadata.html#annotations-strategy
	// or take a look at abstract classes provided by Phalconmerce

	public function initialize() {
		parent::initialize();

		// You can add here instructions that will be executed by the framework, after construction

		// Set the DB table related to this class
		$this->setSource('##SOURCE##');

		##FOREIGN_KEYS##
	}

	/**
	 * Method called automatically by backend controller to konw which fields should be display in controller index (list)
	 * @return array
	 */
	public static function getBackendListProperties() {
		return array(
			// TODO set properties to display in backend list view
			// 'myProperty' => 'Label', // simple property
			// 'mySecondProperty' => array( // property with no human readable value
			//	'label' => 'Label displayed',
			//	'values' => array(
			//		0 => 'unknown',
			//		1 => 'First possible value',
			//		2 => 'Second possible value',
			//		3 => 'Third possible value',
			//      )
			// ),
			// 'status' => array(
			//      'label' => 'Status',
			//      'values' => BackendService::getBackendListStatusValues()
			// )
		);
	}

	/**
	 * Static method returning possibles datas in <select> tag for the field "example"
	 * @return array
	 */
	/*public static function exampleSelectOptions() {
		return array(
			0 => '-',
			1 => 'first option',
			2 => 'second option',
			// etc.
		);
	}*/

	/**
	 * Static method returning fkSelect object used to generated <select> tag in form where category is a foreign key
	 * @return FkSelect
	 */
	/*public static function fkSelect() {
		// change properties list here
		$displayedProperties = array(
			'name'
		);
		return new FkSelect('id', '%s', '##NAMESPACE##\\##CLASSNAME##', $displayedProperties);
	}*/
}

EOT;
		return str_replace(
			array(
				'##NAMESPACE##',
				'##ABSTRACT_NAMESPACE##',
				'##CLASSNAME##',
				'##ABSTRACT_CLASSNAME##',
				'##PROPERTIES##',
				'##SOURCE##',
				'##FOREIGN_KEYS##'
			),
			array(
				self::POPO_NAMESPACE,
				self::POPO_ABSTRACT_NAMESPACE,
				$this->className,
				$this->extendedClassName,
				$propertiesContent,
				$this->tableName,
				$foreignKeysContent
			),
			$phpContent
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
					$abstractClassesList[str_replace('Abstract', '', $abstractClassName)] = $abstractClassName;
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

	/**
	 * @param string $fullyQualifiedClassName
	 * @return bool|\Phalcon\Annotations\Collection
	 */
	public static function getClassAnnotations($fullyQualifiedClassName) {
		$reader = new \Phalcon\Annotations\Adapter\Memory();
		// Reflect the annotations in the class Example
		$reflector = $reader->get($fullyQualifiedClassName);

		return $reflector->getClassAnnotations();
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