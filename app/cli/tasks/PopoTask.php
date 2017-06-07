<?php

namespace Cli\Tasks;

use Cli\Models\Task;
use Phalconmerce\Models\Popo\Popogenerator\PhpClass;
use Phalconmerce\Models\Popo\Popogenerator\PhpProductClass;
use Phalconmerce\Models\Popo\Popogenerator\Property;
use Phalconmerce\Models\Popo\Popogenerator\Relationship;
use Phalconmerce\Models\Popo\Popogenerator\RelationshipManyToMany;
use Phalconmerce\Models\Utils;

class PopoTask extends Task {
	public function mainAction() {
		echo PHP_EOL;
		echo "You have 3 CLI tools available for task \"popo\" :" . PHP_EOL;
		echo "- \"POPO Relationships\" for getting all relationships between classes (mandatory and must be done before others CLI tools)" . PHP_EOL;
		echo "      php app/cli.php popo relationships" . PHP_EOL;
		echo "- \"POPO Generator\" for generating empty POPO Classes (mandatory)" . PHP_EOL;
		echo "      php app/cli.php popo generator" . PHP_EOL;
		echo "- \"POPO Product Generator\" for generating empty POPO Product Classes (also mandatory)" . PHP_EOL;
		echo "      php app/cli.php popo productgenerator" . PHP_EOL;
	}

	public function relationshipsAction($params) {
		// First, create cache directory
		if (!file_exists($this->getDI()->getShared('configPhalconmerce')->cacheDir)) {
			mkdir($this->getDI()->getShared('configPhalconmerce')->cacheDir);
		}

		// All abstract classes to override
		$abstractClassesList = PhpClass::getAbstractClasses();

		if (sizeof($abstractClassesList) > 0) {
			$relationshipsList = array();
			foreach ($abstractClassesList as $currentClassName=>$currentAbstractClassName) {
				// Generate FCQN
				$fqcn = PhpClass::POPO_ABSTRACT_NAMESPACE.'\\'.$currentAbstractClassName;

				// Get properties
				$propertiesList = PhpClass::getClassProperties($fqcn);

				// Initialize nmRelationships
				$nmRelationshipsList = array();

				// Search for FK in abstract class properties
				foreach ($propertiesList as $currentPropertyName=>$currentPropertyReflection) {
					// 1:n or n:1
					if (Property::isForeignKeyFromName($currentPropertyName)) {
						$currentPropertyObject = new Property($currentPropertyName);
						// First way
						$relationshipsList[$currentClassName][$currentPropertyName] = new Relationship(
							$currentPropertyObject->getName(),
							$currentClassName,
							$currentPropertyObject->getForeignKeyPropertyName(),
							addslashes(PhpClass::POPO_NAMESPACE.'\\'.$currentPropertyObject->getForeignKeyClassName()),
							Relationship::TYPE_MANY_TO_1
						);

						// Check if it can be a nmTable (FK is also PK)
						if ($currentPropertyReflection->has('Primary')) {
							$nmRelationshipsList[$currentPropertyObject->getForeignKeyClassName()] = new RelationshipManyToMany(
								$currentPropertyObject->getName(),
								$currentPropertyObject->getForeignKeyClassName(),
								'id', // TODO really get the property name
								'id', // TODO really get the property name
								addslashes(PhpClass::POPO_NAMESPACE.'\\'.$currentClassName)
							);
						}
						else {
							// Second way
							$relationshipsList[$currentPropertyObject->getForeignKeyClassName()][$currentPropertyObject->getForeignKeyPropertyName()] = new Relationship(
								$currentPropertyObject->getForeignKeyPropertyName(),
								$currentPropertyObject->getForeignKeyClassName(),
								$currentPropertyName,
								addslashes(PhpClass::POPO_NAMESPACE.'\\'.$currentClassName),
								Relationship::TYPE_1_TO_MANY
							);
						}
					}
				}

				// If there is nmRelationships, then, add it to relationships array
				if (sizeof($nmRelationshipsList) >= 2) {
					foreach ($nmRelationshipsList as $key=>$currentNmRelationship) {
						/** @var RelationshipManyToMany $currentNmRelationship */
						foreach ($nmRelationshipsList as $key2=>$currentNmRelationship2) {
							/** @var RelationshipManyToMany $currentNmRelationship2 */
							if ($key != $key2) {
								$currentNmRelationship->setExternalPropertyName($currentNmRelationship2->getPropertyName());
								$currentNmRelationship->setExternalFQCN(addslashes(PhpClass::POPO_NAMESPACE.'\\'.$currentNmRelationship2->getClassName()));
								$nmRelationshipsList[$key] = $currentNmRelationship;
							}
						}
					}
					// Now we add it to the $relationshipsList array
					foreach ($nmRelationshipsList as $key=>$currentNmRelationship) {
						$relationshipsList[$key][$currentNmRelationship->getExternalFQCN()] = $currentNmRelationship;
					}
				}
			}

			// Store relationships in data
			if (Utils::saveData($relationshipsList, Relationship::DATA_FILENAME)) {
				echo 'Relationships data generation ok'.PHP_EOL;
				echo 'Now you can generate POPO Classes'.PHP_EOL;
			}
			else {
				echo 'Relationships data generation failed'.PHP_EOL;
			}
		}
		else {
			echo 'No Phalconmerce abstract classes in your project'.PHP_EOL;
		}
	}

	public function generatorAction($params) {
		// First of all, Load relationshps
		$relationshipsList = Utils::loadData(Relationship::DATA_FILENAME);
		if (!isset($relationshipsList) || $relationshipsList === false || !is_array($relationshipsList)) {
			echo PHP_EOL;
			echo 'No relationships generated yet. You must execute "POPO Relationships" CLI tool before any other.'.PHP_EOL;
			$this->mainAction();
			exit;
		}

		// Get options passed to CLI
		$options = $this->console->getOptions();

		// All abstract classes to override
		$abstractClassesList = PhpClass::getAbstractClasses();

		// Si toutes les classes
		if (isset($options['a']) || isset($options['all'])) {
			$selectedClasses = $abstractClassesList;
		}
		else if (sizeof($params) > 0) {
			$askedTables = $params;

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
				// Prepare the class generation
				$phpClass = new PhpClass($currentNewClass, $currentAbstractClass);

				// If a prefix is given for tables' name
				if (array_key_exists('table-prefix', $options)) {
					$phpClass->initTableName($options['table-prefix']);
				}
				else {
					$phpClass->initTableName();
				}

				$currentPhpContent = $phpClass->getPhpContent();

				if ($phpClass->save($currentPhpContent)) {
					echo $phpClass->getClassName().' class file generated'.PHP_EOL;
				}
				else {
					echo 'ERROR : Can\'t create class "'.$phpClass->getClassName().'"'.PHP_EOL;
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
				$phpClass = new PhpProductClass($className);
				$phpClass->initTableName();

				$coreType = self::askQuestion('Choose your Product Type ['.PhpProductClass::CORE_TYPE_SIMPLE_PRODUCT.'=>Simple Product, '.PhpProductClass::CORE_TYPE_CONFIGURABLE_PRODUCT.'=Configurable Product, '.PhpProductClass::CORE_TYPE_GROUPED_PRODUCT.'=Grouped Product] :', array(1,2,3));
				$phpClass->setExtendedClassNameFromCoreTypeResponse($coreType);

				$abstractColumnsList = array(
					'id',
					'sku',
					'price vat excluded',
					'weight',
					'stock',
					'name',
					'short description',
					'description',
					'status'
				);

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
						$propertyObject->setType(self::askQuestion('Its type ['.$questionValues.'] ?', array_keys(Property::$phpTypesList)));

						// Size
						if ($propertyObject->isNumeric()) {
							$propertyObject->setLength(self::askQuestion('Its size [empty for automatic sizing] ?', array(), 0));
						}
						else if ($propertyObject->getType() == 3) { // string
							$propertyObject->setLength(self::askQuestion('Its size (maximum characters) ?'));
						}

						// Translate
						if ($propertyObject->getType() == 3) { // string
							$response = self::askQuestion('Does this property needs to be translated [yes/no] ?');
							$propertyObject->setTranslate($response == 'yes' || $response == 'y');
						}

						// Ask for extra parameters
						$response = self::askQuestion('Do you want to setup extra parameters for this property (signed/unsigned, default value, unique, nullable) [yes/no] ?');
						if ($response == 'yes' || $response == 'y') {
							// Unsigned
							if ($propertyObject->isNumeric()) {
								$response = self::askQuestion('Unsigned or not [1=unsigned, 2=signed] ?', array(1, 2));
								$propertyObject->setUnsigned($response == 1);
							}

							// Default
							$propertyObject->setDefault(self::askQuestion('Its default value (value or SQL expression) ?'));

							// Unique
							$response = self::askQuestion('In database, does this property should be UNIQUE [yes/no] ?');
							$propertyObject->setUnique($response == 'yes' || $response == 'y');

							// Nullable
							$response = self::askQuestion('In database, can this property have NULL values [yes/no] ?');
							$propertyObject->setNullable($response == 'yes' || $response == 'y');
						}
						else {
							// default values for parameters
							$propertyObject->setUnsigned(false);
							$propertyObject->setDefault('');
							$propertyObject->setUnique(false);
							$propertyObject->setNullable(true);
						}

						$phpClass->addProperty($propertyObject);

						print 'Property "'.$propertyName.'"" added.'.PHP_EOL.PHP_EOL;
					}
				}

				$currentPhpContent = $phpClass->getPhpContent();

				if ($phpClass->save($currentPhpContent)) {
					echo $phpClass->getClassName().' class file generated'.PHP_EOL;
				}
				else {
					echo 'ERROR : Can\'t create class "'.$phpClass->getClassName().'"'.PHP_EOL;
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
		echo '  --table-prefix=prefix'.self::TAB_CHARACTER.'to prefix every related tables'.PHP_EOL;
		exit;
	}

	private static function displayProductGeneratorHelp() {
		echo 'Phalconmerce tool for generate empty POPO Product Classes'.PHP_EOL.PHP_EOL;
		echo 'Usage :'.PHP_EOL;
		echo '  php app/cli.php popo productgenerator ProductClassName'.PHP_EOL.PHP_EOL;
		exit;
	}
}