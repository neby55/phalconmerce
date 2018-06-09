<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Forms;

use Phalcon\Di;

class Placeholders {
	/**
	 * @var string[]
	 */
	protected $placeholdersList;

	public function __construct($className) {
		$this->placeholdersList = array();
		if (!empty($className)) {
			$filename = APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR . 'placeholders.csv';

			if (file_exists($filename)) {
				if (($fp = fopen($filename, "r")) !== false) {
					while (($data = fgetcsv($fp, 1024, ";")) !== false) {
						if (strtolower($data[0]) == strtolower($className) || $data[0] == '*') {
							$this->placeholdersList[strtolower($data[1])] = $data[2];
						}
					}
					fclose($fp);
				}
			}
			else {
				Di::getDefault()->get('logger')->warning('placeholders.csv file does not exists');
			}
		}
	}

	/**
	 * @param string $propertyName
	 * @return bool
	 */
	public function propertyExists($propertyName) {
		$propertyLowerName = strtolower($propertyName);
		return array_key_exists($propertyLowerName, $this->placeholdersList);
	}

	/**
	 * @param string $propertyName
	 * @return bool
	 */
	public function getText($propertyName) {
		$propertyLowerName = strtolower($propertyName);
		if (array_key_exists($propertyLowerName, $this->placeholdersList)) {
			return $this->placeholdersList[$propertyLowerName];
		}

		return false;
	}
}