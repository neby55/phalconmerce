<?php

use Phalconmerce\AbstractModel;
use Phalconmerce\Popo\TableGenerator\Table;
use Phalconmerce\Utils;

class TableCreatorController extends \Phalcon\Mvc\Controller {

	public function indexAction() {
		if ($handle = opendir($this->getDI()->get('configPhalconmerce')->modelsDir)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..' && substr($entry, -4) == '.php') {
					$currentClassName =  substr($entry, 0, -4);
					$fqcn = \Phalconmerce\Popo\Popogenerator\PhpClass::POPO_NAMESPACE.'\\'.$currentClassName;

					include_once $this->getDI()->get('configPhalconmerce')->modelsDir . DIRECTORY_SEPARATOR . $entry;

					// Get the object
					/** @var AbstractModel $currentObject */
					$currentObject = new $fqcn;

					// Get properties
					$properties = \Phalconmerce\Popo\Popogenerator\PhpClass::getClassProperties($fqcn);

					// Get table name from class name
					$tableObject = new Table(Utils::getTableNameFromClassName($currentClassName));

					if (sizeof($properties) > 0) {
						foreach ($properties as $currentPropertyName=>$currentPropertyReflect) {
							if ($tableObject->addByAnnotations($currentPropertyName, $currentPropertyReflect, $currentObject->getPrefix())) {
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
				}
			}
			closedir($handle);
		}
	}
}

