<?php

namespace Phalconmerce\Popo\Popogenerator;

use Phalcon\Di;
use Phalconmerce\Utils;

class PhpClass {
	/** @var string */
	public $className;
	/** @var string */
	public $extendedClassName;
	/** @var string */
	public $tableName;
	/** @var Property[] */
	public $propertiesList;

	/** @var array */
	protected static $abstractProductClassesList = array(
		'AbstractConfigurableProduct',
		'AbstractConfiguredProduct',
		'AbstractProduct',
		'AbstractSimpleProduct'
	);

	const CORE_TYPE_SIMPLE_PRODUCT = 1;
	const CORE_TYPE_CONFIGURABLE_PRODUCT = 2;
	const CORE_TYPE_GROUPED_PRODUCT = 3;
	const TAB_CHARACTER = "\t";

	public function __construct($className, $extendedClassName='', $tableName='') {
		$this->className = $className;
		$this->extendedClassName = $extendedClassName;
		$this->tableName = $tableName;
	}

	/**
	 * @return string
	 */
	public function getPhpContent() {
		$phpContent = '<?php'.PHP_EOL.PHP_EOL;
		$phpContent .= 'namespace Phalconmerce\\Popo;'.PHP_EOL.PHP_EOL;
		$phpContent .= 'use Phalconmerce\\Popo\\Abstracts\\%s;'.PHP_EOL.PHP_EOL;
		$phpContent .= 'class %s extends %s {'.PHP_EOL;
		if (is_array($this->propertiesList) && sizeof($this->propertiesList) > 0) {
			$phpContent .= self::TAB_CHARACTER.'/** Properies generated with utils/popo_product_generator.php script */'.PHP_EOL;
			foreach ($this->propertiesList as $currentProperty) {
				$phpContent .= $currentProperty->getPhpContent(self::TAB_CHARACTER);
			}
			$phpContent .= PHP_EOL;
		}
		$phpContent .= self::TAB_CHARACTER.'// Add here your own properties'.PHP_EOL;
		$phpContent .= self::TAB_CHARACTER.'// See the extended Class to know current herited properties'.PHP_EOL;
		$phpContent .= self::TAB_CHARACTER.'// To understand Annotations you must provide to your class, see https://docs.phalconphp.com/en/3.0.0/reference/models-metadata.html#annotations-strategy'.PHP_EOL;
		$phpContent .= PHP_EOL;
		$phpContent .= self::TAB_CHARACTER.'public function initialize() {'.PHP_EOL;
		$phpContent .= self::TAB_CHARACTER.self::TAB_CHARACTER.'parent::initialize();'.PHP_EOL.PHP_EOL;
		$phpContent .= self::TAB_CHARACTER.self::TAB_CHARACTER.'// You can add here instructions that will be executed by the framework, after construction'.PHP_EOL.PHP_EOL;
		$phpContent .= self::TAB_CHARACTER.self::TAB_CHARACTER.'// Uncomment the following line to specify the table name'.PHP_EOL;
		$phpContent .= self::TAB_CHARACTER.self::TAB_CHARACTER.'// $this->setSource(\'%s\');'.PHP_EOL;
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

	public function setExtendedClassNameFromCoreTypeResponse($coreProductType) {
		if ($coreProductType == self::CORE_TYPE_SIMPLE_PRODUCT) {
			$this->extendedClassName = 'AbstractSimpleProduct';
		}
		else if ($coreProductType == self::CORE_TYPE_CONFIGURABLE_PRODUCT) {
			$this->extendedClassName = 'AbstractConfigurableProduct';
		}
		else if ($coreProductType == self::CORE_TYPE_GROUPED_PRODUCT) {
			$this->extendedClassName = 'AbstractGroupedProduct';
		}
	}

	/**
	 * @param string $content
	 * @return int
	 */
	public function save($content) {
		$currentNewClassFilename = self::getPopoDirectory().DIRECTORY_SEPARATOR.$this->className.'.php';
		return file_put_contents($currentNewClassFilename, $content);
	}

	public function initTableName() {
		$this->tableName = Utils::getTableNameFromClassName($this->className);
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
					if (!in_array($abstractClassName, self::$abstractProductClassesList)) {
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
		return DI::getDefault()->get('configPhalconmerce')->modelsDir;
	}
}