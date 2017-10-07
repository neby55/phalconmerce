<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 03/02/2017
 * Time: 13:11
 */

namespace Phalconmerce\Models;

use Phalcon\Mvc\Model;

class AbstractModel extends Model {
	/**
	 * Timestamp representing row creation datetime
	 *
	 * @Column(type="timestamp", nullable=false, default=CURRENT_TIMESTAMP)
	 * @var string
	 */
	public $inserted;

	public function initialize() {
		// Setting up the table name from current Class Name
		$classname = (new \ReflectionClass($this))->getShortName();
		$this->setSource(Utils::getTableNameFromClassName($classname));
	}

	/**
	 * @param array $propertiesList
	 */
	public static function __set_state($propertiesList) {
		$object = new self();
		foreach ($propertiesList as $currentProperty=>$currentValue) {
			$object->$currentProperty = $currentValue;
		}
	}

	/**
	 * @return array
	 */
	public static function getBackendListProperties() {
		return array();
	}
}