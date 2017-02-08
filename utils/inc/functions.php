<?php
/**
 * @return array
 */
function getAbstractProductClasses() {
	return array(
		'AbstractConfigurableProduct',
		'AbstractConfiguredProduct',
		'AbstractProduct',
		'AbstractSimpleProduct'
	);
}

/**
 * @return array
 */
function getAbstractClasses() {
	$abstractClassesList = array();
	if ($handle = opendir(POPO_DIRECTORY.DIRECTORY_SEPARATOR.'abstracts')) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != '.' && $entry != '..' && substr($entry, -4) == '.php') {
				$abstractClassName = substr($entry, 0, -4);
				// We cannot generate Product, there is another script to do that
				if (!in_array($abstractClassName, array('AbstractProduct', 'AbstractConfigurableProduct', 'AbstractConfiguredProduct', 'AbstractProduct', 'AbstractSimpleProduct'))) {
					$abstractClassesList[str_replace('Abstract', '', $abstractClassName)] = $abstractClassName;
				}
			}
		}
	}
	return $abstractClassesList;
}

/**
 * @param string $question
 * @param array $acceptedValues
 * @param string $defaultValue
 * @return string
 */
function askQuestion($question, $acceptedValues=array(), $defaultValue='') {
	print $question.' ';
	$response = trim(fgets(STDIN));
	if (sizeof($acceptedValues) > 0) {
		if (!in_array($response, $acceptedValues)) {
			print 'This response is incorrect.'.PHP_EOL;
			return askQuestion($question, $acceptedValues, $defaultValue);
		}
		else {
			return $defaultValue != '' ? $defaultValue : $response;
		}
	}
	else {
		return $defaultValue != '' ? $defaultValue : $response;
	}
}
