<?php

namespace Cli\Tasks;

use Cli\Models\Task;
use Phalconmerce\Models\Popo\Generators\Popo\PhpClass;
use Phalconmerce\Models\Popo\Generators\Popo\Property;
use Phalconmerce\Models\Popo\Generators\Popo\Relationship;
use Phalconmerce\Models\Popo\Generators\Popo\RelationshipManyToMany;
use Phalconmerce\Models\Utils;

class PopoTask extends Task {
	public function mainAction() {
		echo PHP_EOL;
		echo "You have 3 CLI tools available for task \"popo\" :" . PHP_EOL;
		echo "- \"POPO Relationships\" for getting all relationships between classes (mandatory and must be done before others CLI tools)" . PHP_EOL;
		echo "      php app/cli.php popo relationships" . PHP_EOL;
		echo "- \"POPO Generator\" for generating empty POPO Classes (mandatory)" . PHP_EOL;
		echo "      php app/cli.php popo generate" . PHP_EOL;
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
						$relationshipsList[strtolower($currentClassName)][$currentPropertyName] = new Relationship(
							$currentPropertyObject->getName(),
							$currentClassName,
							$currentPropertyObject->getForeignKeyPropertyName(),
							addslashes(PhpClass::POPO_NAMESPACE.'\\'.$currentPropertyObject->getForeignKeyClassName()),
							Relationship::TYPE_MANY_TO_1
						);

						// Check if it can be a nmTable (FK is also PK)
						if ($currentPropertyReflection->has('Primary')) {
							$nmRelationshipsList[strtolower($currentPropertyObject->getForeignKeyClassName())] = new RelationshipManyToMany(
								$currentPropertyObject->getName(),
								$currentPropertyObject->getForeignKeyClassName(),
								'id', // TODO really get the property name
								'id', // TODO really get the property name
								addslashes(PhpClass::POPO_NAMESPACE.'\\'.$currentClassName)
							);
						}
						else {
							// Second way
							$relationshipsList[strtolower($currentPropertyObject->getForeignKeyClassName())][$currentPropertyObject->getForeignKeyPropertyName()] = new Relationship(
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
						$relationshipsList[strtolower($key)][$currentNmRelationship->getExternalFQCN()] = $currentNmRelationship;
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

	public function generateAction($params) {
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
				$this->displayGenerateHelp();
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
			$this->displayGenerateHelp();
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
			$this->displayGenerateHelp();
		}
	}

	private static function displayGenerateHelp() {
		echo 'Phalconmerce tool for generate empty POPO Classes'.PHP_EOL;
		echo 'for Phalconmerce Abstract Classes'.PHP_EOL.PHP_EOL;
		echo 'Usage :'.PHP_EOL;
		echo '  php app/cli.php [options] popo generate [<ClassName> [<ClassName>...]]'.PHP_EOL.PHP_EOL;
		echo 'Options :'.PHP_EOL;
		echo '  --all'.self::TAB_CHARACTER.'to generate every Classes'.PHP_EOL;
		echo '  --table-prefix=prefix'.self::TAB_CHARACTER.'to prefix every related tables'.PHP_EOL;
		exit;
	}
}