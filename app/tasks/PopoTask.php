<?php

use Phalconmerce\Cli\Task;
use Phalconmerce\Popo\Popogenerator\PhpClass;
use Phalconmerce\Popo\Popogenerator\Property;

class PopoTask extends Task {
	public function mainAction() {
		echo PHP_EOL;
		echo "You have 2 CLI tools available for task \"popo\" :" . PHP_EOL;
		echo "- \"POPO Generator\" for generating empty POPO Classes (mandatory)" . PHP_EOL;
		echo "      php app/cli.php popo generator" . PHP_EOL;
		echo "- \"POPO Product Generator\" for generating empty POPO Product Classes (also mandatory)" . PHP_EOL;
		echo "      php app/cli.php popo productgenerator" . PHP_EOL;
	}

	public function generatorAction($params) {
		$options = $this->console->getOptions();

		// All abstract classes to override
		$abstractClassesList = PhpClass::getAbstractClasses();

		// Si toutes les classes
		if (isset($options['a']) || isset($options['all'])) {
			$selectedClasses = $abstractClassesList;
		}
		else if (sizeof($params) > 0) {
			$askedTables = $params;
			print_r($askedTables);

			if (sizeof($askedTables) <= 0) {
				echo 'No table specified'.PHP_EOL;
				$this->displayGeneratorHelp();
			}
			else {
				foreach ($abstractClassesList as $currentNewClass=>$currentAbstractClass) {
					if (in_array($currentNewClass, $askedTables) || in_array($currentAbstractClass, $askedTables)) {
						$selectedClasses[$currentNewClass] = $currentAbstractClass;
					}
				}
			}
		}
		else {
			$this->displayGeneratorHelp();
		}

		if (isset($selectedClasses) && sizeof($selectedClasses) > 0) {
			foreach ($selectedClasses as $currentNewClass=>$currentAbstractClass) {
				$phpClass = new PhpClass($currentNewClass, $currentAbstractClass);
				$phpClass->initTableName();

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
			echo 'ERROR : No class specified'.PHP_EOL.PHP_EOL;
			$this->displayGeneratorHelp();
		}
	}

	public function productGeneratorAction($params) {
		// All abstract classes to override
		$abstractClassesList = PhpClass::getAbstractClasses();
		if (sizeof($params) > 0) {
			$askedClassNameList = $params;

			if (sizeof($askedClassNameList) <= 0) {
				echo 'No class name specified'.PHP_EOL;
				$this->displayProductGeneratorHelp();
			}
			else {
				$className = current($askedClassNameList);
				$phpClass = new PhpClass($className);
				$phpClass->initTableName();

				$coreType = self::askQuestion('Choose your Product Type ['.PhpClass::CORE_TYPE_SIMPLE_PRODUCT.'=>Simple Product, '.PhpClass::CORE_TYPE_CONFIGURABLE_PRODUCT.'=Configurable Product, '.PhpClass::CORE_TYPE_GROUPED_PRODUCT.'=Grouped Product] :', array(1,2,3));
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
				print self::TAB_CHARACTER.join(PHP_EOL.self::TAB_CHARACTER, $abstractColumnsList).PHP_EOL;
				$propertyName = '';
				while ($propertyName != 'quit') {
					$propertyName = self::askQuestion('What property do you want to add to your object [quit to stop adding properties] ?');
					if ($propertyName != 'quit') {
						$propertyObject = new Property($propertyName);

						// Type
						$questionValues = '';
						foreach (Property::$phpTypesList as $curValue=>$curLabel) {
							$questionValues .= $curValue.'='.$curLabel.',';
						}
						$questionValues = substr($questionValues, 0, -1);
						$propertyObject->type = self::askQuestion('Its type ['.$questionValues.'] ?', array_keys(Property::$phpTypesList));

						// Size
						if ($propertyObject->isNumeric()) {
							$propertyObject->length = self::askQuestion('Its size [empty for automatic sizing] ?', array(), 0);
						}
						else if ($propertyObject->type == 'string') {
							$propertyObject->length = self::askQuestion('Its size (maximum characters) ?');
						}

						// Unsigned
						if ($propertyObject->isNumeric()) {
							$response = self::askQuestion('Unsigned or not [1=unsigned, 2=signed] ?', array(1,2));
							$propertyObject->unsigned = $response == 1;
						}

						// Default
						$propertyObject->default = self::askQuestion('Its default value (value or SQL expression) ?');

						// Unique
						$response = self::askQuestion('In database, does this property should be UNIQUE [yes/no] ?');
						$propertyObject->unique = $response == 'yes' || $response == 'y';

						// Nullable
						$response = self::askQuestion('In database, can this property have NULL values [yes/no] ?');
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
			$this->displayProductGeneratorHelp();
		}
	}

	private static function displayGeneratorHelp() {
		echo 'Phalconmerce tool for generate empty POPO Classes (excepting Product)'.PHP_EOL;
		echo 'for Phalconmerce Abstract Classes'.PHP_EOL.PHP_EOL;
		echo 'Usage :'.PHP_EOL;
		echo '  php app/cli.php [options] popo generator [classe_name[ classe_name2[...]]]'.PHP_EOL.PHP_EOL;
		echo 'Options :'.PHP_EOL;
		echo '  --all'.self::TAB_CHARACTER.'to generate every Classes'.PHP_EOL;
		echo '  --table-prefix=prefix'.self::TAB_CHARACTER.'to prefix every generated tables'.PHP_EOL;
		exit;
	}

	private static function displayProductGeneratorHelp() {
		echo 'Phalconmerce tool for generate empty POPO Product Classes'.PHP_EOL.PHP_EOL;
		echo 'Usage :'.PHP_EOL;
		echo '  php app/cli.php productgenerator ProductClassName'.PHP_EOL.PHP_EOL;
		exit;
	}
}