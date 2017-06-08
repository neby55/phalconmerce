<?php

namespace Cli\Tasks;

use Cli\Models\Task;
use Phalconmerce\Models\AbstractModel;
use Phalconmerce\Models\Popo\TableGenerator\Table;
use Phalconmerce\Models\Utils;

class TableTask extends Task {
	public function mainAction() {
		echo PHP_EOL;
		echo "You have 1 CLI tool available for task \"table\" :" . PHP_EOL;
		echo "- \"Table setup\" for creating or altering tables on configured database" . PHP_EOL;
		echo self::TAB_CHARACTER."php app/cli.php table setup" . PHP_EOL;
	}

	public function setupAction($params) {
		// Get options passed to CLI
		$options = $this->console->getOptions();

		// If table deletion asked, ask for confirmation
		$deletion = false;
		if (array_key_exists('delete', $options)) {
			$response = self::askQuestion('Are you sure to delete table(s) [yes/no] ?');
			$deletion = $response == 'yes' || $response == 'y';
		}

		//print_r($options);
		//print_r($params);
		//exit;

		// initialize array containing all classes
		$classNamesList = array();

		// If --all option
		if (array_key_exists('all', $options)) {
			if ($handle = opendir($this->getDI()->get('configPhalconmerce')->popoModelsDir)) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != '.' && $entry != '..' && substr($entry, -4) == '.php') {
						$classNamesList[] = substr($entry, 0, -4);
					}
				}
				closedir($handle);
			}
		}
		else if (sizeof($params) > 0) {
			$classNamesList = $params;
		}

		if (sizeof($classNamesList) > 0) {
			foreach ($classNamesList as $currentClassName) {
				$fullPathToFile = $this->getDI()->get('configPhalconmerce')->popoModelsDir . DIRECTORY_SEPARATOR . $currentClassName.'.php';
				if (file_exists($fullPathToFile)) {
					echo '0';
					$fqcn = \Phalconmerce\Models\Popo\Popogenerator\PhpClass::POPO_NAMESPACE . '\\' . $currentClassName;
					echo '1';

					include_once $fullPathToFile;

					echo '2';

					// Get the object
					/** @var AbstractModel $currentObject */
					$currentObject = new $fqcn;
					echo 'a';

					// Get properties
					$properties = \Phalconmerce\Models\Popo\Popogenerator\PhpClass::getClassProperties($fqcn);
					echo 'b';

					// Get table name from class name
					$tableObject = new Table(Utils::getTableNameFromClassName($currentClassName), $currentObject->getPrefix());
					echo 'c';

					if (sizeof($properties) > 0) {
						foreach ($properties as $currentPropertyName => $currentPropertyReflect) {
							if (!in_array($currentPropertyName, Table::$excludedPropertyNamesList)) {
								if (!$tableObject->addByAnnotations($currentPropertyName, $currentPropertyReflect)) {
									echo 'property ' . $currentPropertyName . ' not in DB'.PHP_EOL;
								}
							}
						}
					}
					echo 'd';

					// If deletion confirmed
					if ($deletion) {
						if (!$tableObject->drop()) {
							echo 'Table '.$tableObject->getTableName().' has not been deleted'.PHP_EOL;
						}
					}

					// Do the job => create or alter table
					$tableObject->morph();

					echo 'Table '.$tableObject->getTableName().' ok'.PHP_EOL;
				}
				else {
					echo 'Class file ' . $currentClassName . '.php does not exists'.PHP_EOL;
					$this->displaySetupHelp();
				}
			}
		}
		else {
			$this->displaySetupHelp();
		}
		echo PHP_EOL;
	}

	private function displaySetupHelp() {
		echo PHP_EOL;
		echo 'Phalconmerce tool for create or alter tables on configured database, and based on POPO classes existing in folder :' . PHP_EOL;
		echo self::TAB_CHARACTER.$this->getDI()->get('configPhalconmerce')->popoModelsDir . PHP_EOL;
		echo PHP_EOL;
		echo 'Usage :' . PHP_EOL;
		echo self::TAB_CHARACTER.'php app/cli.php [options] table <ClassName>' . PHP_EOL . PHP_EOL;
		echo 'Options :' . PHP_EOL;
		echo self::TAB_CHARACTER.'--all' . self::TAB_CHARACTER . 'to create or alter table for every classes' . PHP_EOL;
		echo self::TAB_CHARACTER.'--delete' . self::TAB_CHARACTER . 'to delete table before creating it (table datas will also be deleted, be careful)' . PHP_EOL;
		echo self::TAB_CHARACTER.'--table-prefix=prefix' . self::TAB_CHARACTER . 'to prefix every related tables' . PHP_EOL;
		echo PHP_EOL;
		echo 'Examples :' . PHP_EOL;
		echo self::TAB_CHARACTER.'# Create or alter all tables' . PHP_EOL;
		echo self::TAB_CHARACTER.'php app/cli.php --all table setup' . PHP_EOL . PHP_EOL;
		echo self::TAB_CHARACTER.'# Create or alter only "orders" table (class "Order")' . PHP_EOL;
		echo self::TAB_CHARACTER.'php app/cli.php table setup Order' . PHP_EOL . PHP_EOL;
		echo self::TAB_CHARACTER.'# Delete table (if exists) and then create only "orders" table (class "Order")' . PHP_EOL;
		echo self::TAB_CHARACTER.'php app/cli.php table setup Order' . PHP_EOL . PHP_EOL;
		echo PHP_EOL;
		exit;
	}
}