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

class Disabled {
	/**
	 * @var string[]
	 */
	protected $disabledList;

	public function __construct($className) {
		$this->disabledList = array();
		if (!empty($className)) {
			$filename = APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR . 'disabled.csv';

			if (file_exists($filename)) {
				if (($fp = fopen($filename, "r")) !== false) {
					while (($data = fgetcsv($fp, 1024, ";")) !== false) {
						if (strtolower($data[0]) == strtolower($className) || $data[0] == '*') {
							$this->disabledList[strtolower($data[1])] = $data[2];
						}
					}
					fclose($fp);
				}
			}
			else {
				Di::getDefault()->get('logger')->info('disabled.csv file does not exists');
			}
		}
	}

	/**
	 * @param string $propertyName
	 * @return bool
	 */
	public function isPropertyDisabled($propertyName) {
		$propertyLowerName = strtolower($propertyName);
		return array_key_exists($propertyLowerName, $this->disabledList);
	}

	/**
	 * @param string $propertyName
	 * @return string
	 */
	public function getPropertyDisabledClass($propertyName) {
		$propertyLowerName = strtolower($propertyName);
		if (array_key_exists($propertyLowerName, $this->disabledList)) {
			return $this->disabledList[$propertyLowerName];
		}
		return $propertyName;
	}
}