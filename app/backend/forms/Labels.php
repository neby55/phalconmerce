<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Forms;


class Labels {
	/**
	 * @var string[]
	 */
	protected $labelsList;

	public function __construct($className) {
		if (!empty($className)) {
			$filename = APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR . 'labels.csv';

			if (file_exists($filename)) {
				if (($fp = fopen($filename, "r")) !== false) {
					while (($data = fgetcsv($fp, 1024, ";")) !== false) {
						if (strtolower($data[0]) == strtolower($data[0])) {
							$this->labelsList[strtolower($data[1])] = array(
								$data[2],
								$data[3]
							);
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
	 * @return string
	 */
	public function getShortLabelForProperty($propertyName) {
		return $this->getLabelForProperty($propertyName, false);
	}

	/**
	 * @param string $propertyName
	 * @return string
	 */
	public function getLongLabelForProperty($propertyName) {
		return $this->getLabelForProperty($propertyName, true);
	}

	/**
	 * @param string $propertyName
	 * @return string
	 */
	private function getLabelForProperty($propertyName, $long=false) {
		$propertyLowerName = strtolower($propertyName);
		if (array_key_exists($propertyLowerName, $this->labelsList)) {
			if ($long) {
				return $this->labelsList[$propertyLowerName][1];
			}
			else {
				return $this->labelsList[$propertyLowerName][0];
			}
		}
		// Status case
		if ($propertyLowerName == 'status') {
			return 'Status';
		}
		return $propertyName;
	}
}