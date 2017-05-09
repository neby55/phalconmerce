<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 09/05/2017
 * Time: 14:33
 */

namespace Phalconmerce\Cli;

class Task extends \Phalcon\Cli\Task {

	const TAB_CHARACTER = "\t";
	/**
	 * @param string $question
	 * @param array $acceptedValues
	 * @param string $defaultValue
	 * @return string
	 */
	public static function askQuestion($question, $acceptedValues=array(), $defaultValue='') {
		print $question.' ';
		$response = trim(fgets(STDIN));
		if (sizeof($acceptedValues) > 0) {
			if (!in_array($response, $acceptedValues)) {
				print 'This response is incorrect.'.PHP_EOL;
				return self::askQuestion($question, $acceptedValues, $defaultValue);
			}
			else {
				return $defaultValue != '' ? $defaultValue : $response;
			}
		}
		else {
			return $defaultValue != '' ? $defaultValue : $response;
		}
	}
}