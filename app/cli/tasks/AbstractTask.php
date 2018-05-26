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

class AbstractTask extends Task {
	public function mainAction() {
		$this->displayHelp();
	}

	private static function getAbstractsDirectories() {
		return array(
			'api'.DIRECTORY_SEPARATOR.'controllers' => 'Api\Controllers',
			'backend'.DIRECTORY_SEPARATOR.'controllers' => 'Backend\Controllers',
			'frontend'.DIRECTORY_SEPARATOR.'controllers' => 'Frontend\Controllers',
			'phalconmerce'.DIRECTORY_SEPARATOR.'plugins' => 'Phalconmerce\Plugins',
			'phalconmerce'.DIRECTORY_SEPARATOR.'services' => 'Phalconmerce\Services',
			'phalconmerce'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'exceptions' => 'Phalconmerce\Models\Exceptions',
			'phalconmerce'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'checkout' => 'Phalconmerce\Models\Checkout',
			'phalconmerce'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'generic' => 'Phalconmerce\Models\Generic',
		);
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

		// initialize specific class to generate
		$classNameSpecificList = array();

		// If --all option
		if (array_key_exists('all', $options)) {
			$classNameSpecificList = array();
		}
		else if (sizeof($params) > 0) {
			$classNameSpecificList = $params;
		}

		$directories = self::getAbstractsDirectories();
		$strTodisplay = '';

		if (sizeof($directories) > 0) {
			try {
				foreach ($directories as $currentDirectory => $currentNamespace) {
					$absoluteTargetDirectory = APP_PATH . DIRECTORY_SEPARATOR . $currentDirectory;
					$absoluteCurrentDirectory = $absoluteTargetDirectory . DIRECTORY_SEPARATOR . 'abstracts';
					if (file_exists($absoluteCurrentDirectory) && is_dir($absoluteCurrentDirectory)) {
						if ($dh = opendir($absoluteCurrentDirectory)) {
							while (($file = readdir($dh)) !== false) {
								if ($file != '.' && $file != '..' && substr($file, 0, 8) == 'Abstract') {
									$abstractClassName = substr($file, 0, -4);
									$childClassName = substr($abstractClassName, 8);

									// If no classname asked or current classname is one of asked
									if (empty($classNameSpecificList) || in_array($childClassName, $classNameSpecificList)) {
										$classFilename = $absoluteTargetDirectory . DIRECTORY_SEPARATOR . $childClassName . '.php';
										// if can't deleted existing class
										if (file_exists($classFilename) && !$deletion) {
											$strTodisplay .= 'Can\'t delete existing child class file ' . $currentDirectory . DIRECTORY_SEPARATOR . $childClassName . '.php' . PHP_EOL;
										}
										else {
											file_put_contents($classFilename, $this->getPhpContent($currentNamespace, $abstractClassName, $childClassName));
											$strTodisplay .= 'Child class file ' . $currentDirectory . DIRECTORY_SEPARATOR . $childClassName . '.php generated' . PHP_EOL;
										}
										flush();
									}
								}
							}
							closedir($dh);
						}
					}
				}
			}
			catch(\Exception $e) {
				die($e->getMessage());
			}
			echo $strTodisplay;
		}
		else {
			$this->displayHelp();
		}
		echo PHP_EOL;
	}

	private function getPhpContent($namespace, $abstractClassname, $childClassname) {
		$phpContent = <<<'EOT'
<?php

namespace ##NAMESPACE##;

use ##NAMESPACE##\Abstracts\##ABSTRACT_CLASSNAME##;

class ##CHILD_CLASSNAME## extends ##ABSTRACT_CLASSNAME## {

}
EOT;
		return str_replace(array('##NAMESPACE##', '##ABSTRACT_CLASSNAME##', '##CHILD_CLASSNAME##'), array($namespace, $abstractClassname, $childClassname), $phpContent);
	}

	private function displayHelp() {
		echo PHP_EOL;
		echo 'Phalconmerce tool for generating empty child class based on Phalconmerce abstract class.' . PHP_EOL;
		echo PHP_EOL;
		echo 'All child classes should be generated for Phalconmerce to be working.' . PHP_EOL;
		echo 'Those abstract classes are located in "abstracts" directories.' . PHP_EOL;
		echo PHP_EOL;
		echo 'Usage :' . PHP_EOL;
		echo self::TAB_CHARACTER . 'php app/cli.php abstract generate [ClassName]' . PHP_EOL . PHP_EOL;
		echo 'Options :' . PHP_EOL;
		echo self::TAB_CHARACTER . '--all' . self::TAB_CHARACTER . 'to generate child classes for every abstract classes' . PHP_EOL;
		echo self::TAB_CHARACTER . '--delete' . self::TAB_CHARACTER . 'to delete existing child class\' files (be careful)' . PHP_EOL;
		echo PHP_EOL;
		echo 'Examples :' . PHP_EOL;
		echo self::TAB_CHARACTER . '# Create all child classes' . PHP_EOL;
		echo self::TAB_CHARACTER . 'php app/cli.php --all abstract generate' . PHP_EOL . PHP_EOL;
		echo self::TAB_CHARACTER . '# Create child class for the abstract class : AbstractBackendService' . PHP_EOL;
		echo self::TAB_CHARACTER . 'php app/cli.php abstract generate BackendService' . PHP_EOL . PHP_EOL;
		echo self::TAB_CHARACTER . '# Create (or replace, if needed) child class for the abstract class : AbstractBackendService' . PHP_EOL;
		echo self::TAB_CHARACTER . 'php app/cli.php --delete abstract generate BackendService' . PHP_EOL . PHP_EOL;
		echo PHP_EOL;
		exit;
	}
}