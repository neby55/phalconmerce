<?php

use Phalcon\Annotations\Adapter\Memory as MemoryAdapter;
use Phalconmerce\Popo\TableGenerator\Table;
use Phalconmerce\Utils;

class TableCreatorController extends \Phalcon\Mvc\Controller {

	public function indexAction() {
		echo 'Category => '.Utils::getTableNameFromClassName('Category').'<br>';
		echo 'Country => '.Utils::getTableNameFromClassName('Country').'<br>';
		echo 'Shipment => '.Utils::getTableNameFromClassName('Shipment').'<br>';
		echo 'other way<br>';
		echo 'invoices => '.Utils::getClassNameFromTableName('invoices').'<br>';
		echo 'order_returns => '.Utils::getClassNameFromTableName('order_returns').'<br>';
		echo 'delivery_delays => '.Utils::getClassNameFromTableName('delivery_delays').'<br>';
		if ($handle = opendir($this->getDI()->get('configPhalconmerce')->phalconmerce->modelsDir)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..' && substr($entry, -4) == '.php') {
					$currentClassName =  substr($entry, 0, -4);

					include_once $this->getDI()->get('configPhalconmerce')->phalconmerce->modelsDir . DIRECTORY_SEPARATOR . $entry;

					$reader = new MemoryAdapter();
					// Reflect the annotations in the class Example
					$reflector = $reader->get('Phalconmerce\\Popo\\'.$currentClassName);
					//echo 'Phalconmerce\\Popo\\'.$currentClassName;
					//Utils::debug($reflector);
					// Get properties
					$properties = $reflector->getPropertiesAnnotations();

					// Get table name from class name
					$tableObject = new Table(Utils::getTableNameFromClassName($currentClassName));

					if (sizeof($properties) > 0) {
						foreach ($properties as $currentPropertyName=>$currentPropertyReflect) {
							//Utils::debug($currentPropertyReflect);

							if ($tableObject->addByAnnotations($currentPropertyName, $currentPropertyReflect)) {
								echo $currentPropertyName.' added<br>';
							}
							else {
								echo 'property '.$currentPropertyName.' not in DB<br>';
							}
							echo '<br>';
						}
					}

					Utils::debug($tableObject);

					$tableObject->morph();
					exit;


					//print_r($properties);
					// Read the annotations in the class' docblock
					$annotations = $reflector->getClassAnnotations();

					// Traverse the annotations
					foreach ($annotations as $annotation) {
						// Print the annotation name
						echo $annotation->getName(), PHP_EOL;

						// Print the number of arguments
						echo $annotation->numberArguments(), PHP_EOL;

						// Print the arguments
						print_r($annotation->getArguments());
					}
				}
			}
			closedir($handle);
		}
	}

	private static function getMySqlType ($type, $length) {
		if ($type == 'integer') {
			if (1 <= $length && $length <= 2) {
				return 'TINYINT';
			}
			else if (3 <= $length && $length <= 4) {
				return 'SMALLINT';
			}
			else if (5 <= $length && $length <= 11) {
				return 'INTEGER';
			}
			else if (12 <= $length && $length <= 20) {
				return 'BIGINT';
			}
		}
		else if ($type == 'float') {
			return 'DECIMAL(16,2)';
		}
		else if ($type == 'string') {
			return 'VARCHAR('.$length.')';
		}
		return strtoupper($type);
	}

}

