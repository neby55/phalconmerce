<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Forms;


use Phalconmerce\Models\Utils;

class HelpBlocks {
	/**
	 * @var string[]
	 */
	protected $helpBlocksList;

	public function __construct($className) {
		$this->helpBlocksList = array();
		if (!empty($className)) {
			$filename = APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR . 'help-blocks.csv';

			if (file_exists($filename)) {
				if (($fp = fopen($filename, "r")) !== false) {
					while (($data = fgetcsv($fp, 1024, ";")) !== false) {
						if (strtolower($data[0]) == strtolower($className)) {
							$this->helpBlocksList[strtolower($data[1])] = $data[2];
						}
					}
				}
			}
			// TODO make logs
			/*else {
				die($filename. ' does not exists');
			}*/
		}
	}

	/**
	 * @param string $propertyName
	 * @return bool
	 */
	public function getText($propertyName) {
		$propertyLowerName = strtolower($propertyName);
		if (array_key_exists($propertyLowerName, $this->helpBlocksList)) {
			return $this->helpBlocksList[$propertyLowerName];
		}

		return false;
	}
}