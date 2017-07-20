<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models\Popo\Generators;

use Phalcon\Di;

class Helper {
	/**
	 * Returns a list of Phalconmerce abstract classes' names
	 * @return array
	 */
	public static function getPopoClassesName() {
		$classNamesList = array();
		if ($handle = opendir(Di::getDefault()->get('configPhalconmerce')->popoModelsDir)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..' && substr($entry, -4) == '.php') {
					$classNamesList[] = substr($entry, 0, -4);
				}
			}
			closedir($handle);
		}

		return $classNamesList;
	}
}