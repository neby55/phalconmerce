<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models;

class DesignParam {
	/** @var string */
	protected $name;
	/** @var int */
	protected $type;

	const TYPE_INT = 1;
	const TYPE_FLOAT = 2;
	const TYPE_STRING = 3;
	const TYPE_BOOLEAN = 4;
	const TYPE_ARRAY = 5;
	const TYPE_OBJECT = 6;
	const TYPE_HTML = 7;

	public function __construct($name='', $type=0) {
		$this->name = $name;
		$this->type = $type;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return !empty($this->name) && !empty($this->type);
	}

	/**
	 * @return array
	 */
	protected static function getTypeNames() {
		return array(
			'int' => self::TYPE_INT,
			'integer' => self::TYPE_INT,
			'float' => self::TYPE_FLOAT,
			'double' => self::TYPE_FLOAT,
			'string' => self::TYPE_STRING,
			'char' => self::TYPE_STRING,
			'bool' => self::TYPE_BOOLEAN,
			'boolean' => self::TYPE_BOOLEAN,
			'array' => self::TYPE_ARRAY,
			'object' => self::TYPE_OBJECT,
			'html' => self::TYPE_HTML,
		);
	}

	/**
	 * @param string $name
	 * @return int|bool
	 */
	public static function getTypeByName($name) {
		$name = strtolower($name);

		$typeNames = self::getTypeNames();
		if (array_key_exists($name, $typeNames)) {
			return $typeNames[$name];
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}
}