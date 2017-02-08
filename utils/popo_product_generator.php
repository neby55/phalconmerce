<?php

require dirname(__FILE__).'/inc/config.php';

// Prints help
function displayHelp() {
	echo 'Phalconmerce tool for generate empty POPO Product Classes'.PHP_EOL.PHP_EOL;
	echo 'Usage :'.PHP_EOL;
	echo '  php popo_product_generator.php ProductClassName'.PHP_EOL.PHP_EOL;
}

// All abstract classes to override
$abstractClassesList = getAbstractProductClasses();
if (sizeof($argv) > 0) {
	$askedClassNameList = array();
	foreach ($argv as $argValue) {
		if (substr($argValue,0,1) != '-') {
			$askedClassNameList[] = $argValue;
		}
	}

	if (sizeof($askedClassNameList) <= 0) {
		echo 'No class name specified'.PHP_EOL;
		displayHelp();
		exit;
	}
	else {
		$className = current($askedClassNameList);
		$phpClass = new PhpClass($className);
		$phpClass->initTableName();

		$coreType = askQuestion('Choose your Product Type ['.PhpClass::CORE_TYPE_SIMPLE_PRODUCT.'=>Simple Product, '.PhpClass::CORE_TYPE_CONFIGURABLE_PRODUCT.'=Configurable Product, '.PhpClass::CORE_TYPE_GROUPED_PRODUCT.'=Grouped Product] :', array(1,2,3));
		$phpClass->setExtendedClassNameFromCoreTypeResponse($coreType);

		$abstractColumnsList = array(
			'id',
			'sku',
			'price_vat_excluded',
			'weight',
			'stock',
			'status',
			'parent_product_id'
		);
		$phpClass->propertiesList = array();
		print 'Those properties are inherited from AbstractProduct :'.PHP_EOL;
		print TAB_CHARACTER.join(PHP_EOL.TAB_CHARACTER, $abstractColumnsList).PHP_EOL;
		$propertyName = '';
		while ($propertyName != 'quit') {
			$propertyName = askQuestion('What property do you want to add to your object [quit to stop adding properties] ?');
			if ($propertyName != 'quit') {
				$propertyObject = new Property($propertyName);

				// Type
				$questionValues = '';
				foreach (Property::$phpTypesList as $curValue=>$curLabel) {
					$questionValues .= $curValue.'='.$curLabel.',';
				}
				$questionValues = substr($questionValues, 0, -1);
				$propertyObject->type = askQuestion('Its type ['.$questionValues.'] ?', array_keys(Property::$phpTypesList));

				// Size
				if ($propertyObject->isNumeric()) {
					$propertyObject->length = askQuestion('Its size [empty for automatic sizing] ?', array(), 0);
				}
				else if ($propertyObject->type == 'string') {
					$propertyObject->length = askQuestion('Its size (maximum characters) ?');
				}

				// Unsigned
				if ($propertyObject->isNumeric()) {
					$response = askQuestion('Unsigned or not [1=unsigned, 2=signed] ?', array(1,2));
					$propertyObject->unsigned = $response == 1;
				}

				// Default
				$propertyObject->default = askQuestion('Its default value (value or SQL expression) ?');

				// Unique
				$response = askQuestion('In database, does this property should be UNIQUE [yes/no] ?');
				$propertyObject->unique = $response == 'yes' || $response == 'y';

				// Nullable
				$response = askQuestion('In database, can this property have NULL values [yes/no] ?');
				$propertyObject->nullable = $response == 'yes' || $response == 'y';

				$phpClass->propertiesList[$propertyName] = $propertyObject;

				print 'Property "'.$propertyName.'"" added.'.PHP_EOL.PHP_EOL;
			}
		}

		$currentPhpContent = $phpClass->getPhpContent();

		if ($phpClass->save($currentPhpContent)) {
			echo $phpClass->className.' class file generated'.PHP_EOL;
		}
		else {
			echo 'ERROR : Can\'t create class "'.$phpClass->className.'"'.PHP_EOL;
		}
	}
}
else {
	displayHelp();
	exit;
}