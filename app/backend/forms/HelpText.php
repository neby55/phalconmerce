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
use Phalconmerce\Models\Utils;

class HelpText {
	/**
	 * @var string[]
	 */
	protected $helpTextsList;

	public function __construct($className) {
		$this->helpTextsList = array();
		if (!empty($className)) {
			$filename = APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR . 'help-text.csv';

			if (file_exists($filename)) {
				if (($fp = fopen($filename, "r")) !== false) {
					while (($data = fgetcsv($fp, 1024, ";")) !== false) {
						if (strtolower($data[0]) == strtolower($className) || $data[0] == '*') {
							$this->helpTextsList[strtolower($data[1])][] = $data[2];
						}
					}
					fclose($fp);
				}
			}
			else {
				Di::getDefault()->get('logger')->warning('help-text.csv file does not exists');
			}
		}
	}

	/**
	 * @param string $actionName
	 * @param string $separator
	 * @return bool|string
	 */
	public function getText($actionName, $separator='</p><p>') {
		$actionLowerName = strtolower($actionName);
		if (array_key_exists($actionLowerName, $this->helpTextsList)) {
			return join($separator, $this->helpTextsList[$actionLowerName]);
		}
		return false;
	}
}