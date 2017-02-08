<?php

class PhpClass {
	/** @var string */
	public $className;
	/** @var string */
	public $extendedClassName;
	/** @var string */
	public $tableName;
	/** @var Property[] */
	public $propertiesList;

	const CORE_TYPE_SIMPLE_PRODUCT = 1;
	const CORE_TYPE_CONFIGURABLE_PRODUCT = 2;
	const CORE_TYPE_GROUPED_PRODUCT = 3;

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
			$phpContent .= TAB_CHARACTER.'/** Properies generated with utils/popo_product_generator.php script */'.PHP_EOL;
			foreach ($this->propertiesList as $currentProperty) {
				$phpContent .= $currentProperty->getPhpContent(TAB_CHARACTER);
			}
			$phpContent .= PHP_EOL;
		}
		$phpContent .= TAB_CHARACTER.'// Add here your own properties'.PHP_EOL;
		$phpContent .= TAB_CHARACTER.'// See the extended Class to know current herited properties'.PHP_EOL;
		$phpContent .= TAB_CHARACTER.'// To understand Annotations you must provide to your class, see https://docs.phalconphp.com/en/3.0.0/reference/models-metadata.html#annotations-strategy'.PHP_EOL;
		$phpContent .= PHP_EOL;
		$phpContent .= TAB_CHARACTER.'public function initialize() {'.PHP_EOL;
		$phpContent .= TAB_CHARACTER.TAB_CHARACTER.'parent::initialize();'.PHP_EOL.PHP_EOL;
		$phpContent .= TAB_CHARACTER.TAB_CHARACTER.'// You can add here instructions that will be executed by the framework, after construction'.PHP_EOL.PHP_EOL;
		$phpContent .= TAB_CHARACTER.TAB_CHARACTER.'// Uncomment the following line to specify the table name'.PHP_EOL;
		$phpContent .= TAB_CHARACTER.TAB_CHARACTER.'// $this->setSource(\'%s\');'.PHP_EOL;
		$phpContent .= TAB_CHARACTER.'}'.PHP_EOL;
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
		$currentNewClassFilename = POPO_DIRECTORY.DIRECTORY_SEPARATOR.$this->className.'.php';
		return file_put_contents($currentNewClassFilename, $content);
	}

	public function initTableName() {
		require_once PATH . DIRECTORY_SEPARATOR . 'app/models/phalconmerce/Utils.php';
		$this->tableName = \Phalconmerce\Utils::getTableNameFromClassName($this->className);
	}
}