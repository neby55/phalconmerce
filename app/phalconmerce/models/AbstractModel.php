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
	 * Prefix for fields in table
	 *
	 * @var string
	 */
	public $prefix;

	/**
	 * Timestamp representing row creation datetime
	 *
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $inserted;

	public function initialize() {
		$classname = (new \ReflectionClass($this))->getShortName();
		$tableName = $classname;

		$this->setSource($tableName);

		// Checking prefix value before setting new value automatically
		if (empty($this->prefix)) {
			$this->setPrefix(Utils::getPrefixFromTableName($tableName));
		}
	}

	public function columnMap() {
		$propertiesList = get_object_vars($this);
		$prefixedList = array();
		foreach($propertiesList as $currentPropertyName=>$currentProprety) {
			// Avoid FactoryDefault property
			if ($currentPropertyName != 'prefix' && substr($currentPropertyName,0,1) != '_') {
				$prefixedList[$this->getPrefix() . $currentPropertyName] = $currentPropertyName;
			}
		}
		return $prefixedList;
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
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}
}