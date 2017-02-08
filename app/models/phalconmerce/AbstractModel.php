<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 03/02/2017
 * Time: 13:11
 */

namespace Phalconmerce;

use Phalcon\Mvc\Model;

class AbstractModel extends Model {
	/**
	 * Prefix for fields in table
	 * "usr_" for example
	 * @var string
	 */
	protected $prefix;

	public function initialize() {
		$classname = (new \ReflectionClass($this))->getShortName();
		$tableName = $classname;

		$this->setSource($tableName);
		$this->setPrefix(substr($tableName,0,3).'_');
	}

	public function columnMap() {
		$propertiesList = get_object_vars($this);
		$prefixedList = array();
		foreach($propertiesList as $currentProprety) {
			$prefixedList[$this->getPrefix().$currentProprety] = $currentProprety;
		}
		return $prefixedList;
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