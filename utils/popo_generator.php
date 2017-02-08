<?php

require dirname(__FILE__).'/inc/config.php';

// Prints help
function displayHelp() {
	echo 'Phalconmerce tool for generate empty POPO Classes (excepting Product) for Phalconmerce Abstract Classes'.PHP_EOL.PHP_EOL;
	echo 'Usage :'.PHP_EOL;
	echo '  php popo_generator.php [options] [classe_name[ classe_name2[...]]]'.PHP_EOL.PHP_EOL;
	echo 'Options :'.PHP_EOL;
	echo '  --all'.TAB_CHARACTER.'to generate every Classes'.PHP_EOL;
	echo '  --table-prefix=prefix'.TAB_CHARACTER.'to prefix every generated tables'.PHP_EOL;
}

$shortOpts = '';
$longOpts  = array(
	"table-prefix:",
	"all",
);
$options = getopt($shortOpts, $longOpts);

// All abstract classes to override
$abstractClassesList = getAbstractClasses();

// Si toutes les classes
if (isset($options['a']) || isset($options['all'])) {
	$selectedClasses = getAbstractClasses();
}
else if (sizeof($argv) > 0) {
	$askedTables = array();
	foreach ($argv as $argValue) {
		if (substr($argValue,0,1) != '-') {
			$askedTables[] = $argValue;
		}
	}

	if (sizeof($askedTables) <= 0) {
		echo 'No table specified'.PHP_EOL;
		displayHelp();
		exit;
	}
	else {
		$abstractClassesList = getAbstractClasses();
		foreach ($abstractClassesList as $currentNewClass=>$currentAbstractClass) {
			if (in_array($currentNewClass, $askedTables) || in_array($currentAbstractClass, $askedTables)) {
				$selectedClasses[$currentNewClass] = $currentAbstractClass;
			}
		}
	}
}
else {
	displayHelp();
	exit;
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
	displayHelp();
	exit;
}