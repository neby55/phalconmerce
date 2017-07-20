<?php

namespace Cli\Tasks;

use Cli\Models\Task;
use Phalconmerce\Models\AbstractModel;
use Phalconmerce\Models\Popo\Generators\Backend\ControllerClass;
use Phalconmerce\Models\Popo\Generators\Backend\FormClass;
use Phalconmerce\Models\Popo\Generators\Backend\ViewsPhtml;
use Phalconmerce\Models\Popo\Generators\Db\Table;
use Phalconmerce\Models\Popo\Generators\Helper;
use Phalconmerce\Models\Utils;

class BackendTask extends Task {
	public function mainAction() {
		$this->displayHelp();
	}

	public function generateAction($params) {
		// Get options passed to CLI
		$options = $this->console->getOptions();

		// If table deletion asked, ask for confirmation
		$deletion = false;
		if (array_key_exists('delete', $options)) {
			$response = self::askQuestion('Are you sure to delete existing file(s) [yes/no] ?');
			$deletion = $response == 'yes' || $response == 'y';
		}

		//print_r($options);
		//print_r($params);
		//exit;

		// initialize array containing all classes
		$classNamesList = array();

		// If --all option
		if (array_key_exists('all', $options)) {
			$classNamesList = Helper::getPopoClassesName();
		}
		else if (sizeof($params) > 0) {
			$classNamesList = $params;
		}

		if (sizeof($classNamesList) > 0) {
			foreach ($classNamesList as $currentClassName) {
				$fullPathToFile = $this->getDI()->get('configPhalconmerce')->popoModelsDir . DIRECTORY_SEPARATOR . $currentClassName . '.php';
				if (file_exists($fullPathToFile)) {
					// Controller
					$controller = new ControllerClass($currentClassName);

					if ($controller->save($controller->getPhpContent())) {
						echo 'Controller generated for ' . $currentClassName . PHP_EOL;
					}
					else {
						echo 'Error in ' . $currentClassName . ' controller generation' . PHP_EOL;
					}

					// Form
					$form = new FormClass($currentClassName);

					if ($form->save($form->getPhpContent())) {
						echo 'Form class generated for ' . $currentClassName . PHP_EOL;
					}
					else {
						echo 'Error in ' . $currentClassName . ' form class generation' . PHP_EOL;
					}

					// View
					$views = new ViewsPhtml($currentClassName);
					if ($views->save()) {
						echo 'View files for '.$currentClassName.' generated'.PHP_EOL;
					}
					else {
						echo 'Error in ' . $currentClassName . ' view files generation' . PHP_EOL;
					}
				}
				else {
					echo 'Class file ' . $currentClassName . '.php does not exists' . PHP_EOL;
					$this->displayHelp();
				}
			}
		}
		else {
			$this->displayHelp();
		}
		echo PHP_EOL;
	}

	private function displayHelp() {
		echo PHP_EOL;
		echo 'Phalconmerce tool for generating standard backend interface based on POPO classes existing in the following folder.' . PHP_EOL;
		echo self::TAB_CHARACTER . $this->getDI()->get('configPhalconmerce')->popoModelsDir . PHP_EOL;
		echo PHP_EOL;
		echo 'Controlles and Forms will be generated and then can be modified' . PHP_EOL;
		echo 'Phalconmerce provide a basic bootstrap backend view, but you can choose or write another one.' . PHP_EOL;
		echo PHP_EOL;
		echo 'Usage :' . PHP_EOL;
		echo self::TAB_CHARACTER . 'php app/cli.php backend generate [ClassName]' . PHP_EOL . PHP_EOL;
		echo 'Options :' . PHP_EOL;
		echo self::TAB_CHARACTER . '--all' . self::TAB_CHARACTER . 'to generate backend for every classes' . PHP_EOL;
		echo self::TAB_CHARACTER . '--delete' . self::TAB_CHARACTER . 'to delete existing files (be careful)' . PHP_EOL;
		echo PHP_EOL;
		echo 'Examples :' . PHP_EOL;
		echo self::TAB_CHARACTER . '# Create all files for the bacnkend interface' . PHP_EOL;
		echo self::TAB_CHARACTER . 'php app/cli.php --all backend generate' . PHP_EOL . PHP_EOL;
		echo self::TAB_CHARACTER . '# Create file for backend interface related to the "Order" class' . PHP_EOL;
		echo self::TAB_CHARACTER . 'php app/cli.php backend generate Order' . PHP_EOL . PHP_EOL;
		echo self::TAB_CHARACTER . '# Create (or replace, if needed) file for backend interface related to the "Order" class' . PHP_EOL;
		echo self::TAB_CHARACTER . 'php app/cli.php --delete backend generate Order' . PHP_EOL . PHP_EOL;
		echo PHP_EOL;
		exit;
	}
}