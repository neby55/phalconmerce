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
	/** @var string */
	protected $help;

	const TYPE_INT = 1;
	const TYPE_FLOAT = 2;
	const TYPE_STRING = 3;
	const TYPE_BOOLEAN = 4;
	const TYPE_ARRAY = 5;
	const TYPE_OBJECT = 6;
	const TYPE_HTML = 7;
	const TYPE_URL = 8;
	const TYPE_IMAGE = 9;

	public function __construct($name='', $type=0, $help='') {
		$this->name = $name;
		$this->type = $type;
		$this->help = $help;
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
			'url' => self::TYPE_URL,
			'img' => self::TYPE_IMAGE,
			'image' => self::TYPE_IMAGE,
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
	public function getFilter() {
		switch ($this->type) {
			case self::TYPE_INT :
			case self::TYPE_BOOLEAN :
				return 'int';
			case self::TYPE_FLOAT :
				return 'float';
			case self::TYPE_STRING :
				return 'string';
			case self::TYPE_HTML :
				return 'html';
			case self::TYPE_URL :
				return 'int';
			case self::TYPE_IMAGE :
				return 'url';
		}
		return '';
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

	/**
	 * @return string
	 */
	public function getHelp() {
		return $this->help;
	}
}