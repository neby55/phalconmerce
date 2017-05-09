<?php

namespace Phalconmerce\Popo\Popogenerator;

class Property {
	/** @var string */
	public $name;
	/** @var int */
	public $type;
	/** @var int */
	public $length;
	/** @var boolean */
	public $unsigned;
	/** @var boolean */
	public $nullable;
	/** @var boolean */
	public $unique;
	/** @var string */
	public $default;

	public static $phpTypesList = array(
		1 => 'int',
		2 => 'float',
		3 => 'string',
		4 => 'boolean'
	);

	const FK_SEPARATOR = '_';

	public function __construct($name) {
		$this->name = $name;
		$this->type = 0;
		$this->length = 0;
		$this->unsigned = true;
		$this->nullable = false;
		$this->unique = false;
		$this->default = '';
	}

	/**
	 * @return string
	 */
	public function getAnnotationType() {
		$typesList = array(
			1 => 'integer',
			2 => 'decimal',
			3 => 'string',
			4 => 'boolean'
		);
		return array_key_exists($this->type, $typesList) ? $typesList[$this->type] : '';
	}

	/**
	 * @return string
	 */
	public function getPhpType() {
		return array_key_exists($this->type, self::$phpTypesList) ? self::$phpTypesList[$this->type] : '';
	}

	/**
	 * @return bool
	 */
	public function isNumeric() {
		return in_array($this->type, array(1,2));
	}

	/**
	 * @return bool
	 */
	public function isForeignKey() {
		return self::isForeignKeyFromName($this->name);
	}

	/**
	 * @param string $propertyNameOrColumnName
	 * @return bool
	 */
	public static function isForeignKeyFromName($propertyNameOrColumnName) {
		return substr($propertyNameOrColumnName, 0, 3) == 'fk'.self::FK_SEPARATOR;
	}

	/**
	 * @param string $startLineCharacter
	 * @return string
	 */
	public function getPhpContent($startLineCharacter='') {
		$content = $startLineCharacter.'/**'.PHP_EOL;
		$content .= $startLineCharacter.' * @Column(type="'.$this->getAnnotationType().'"';
		if ($this->length > 0) {
			$content .= ', length='.$this->length;
		}
		if ($this->isNumeric() && $this->unsigned) {
			$content .= ', unsigned=true';
		}
		if ($this->unique) {
			$content .= ', unique=true';
		}
		if ($this->default) {
			$content .= ', default="'.str_replace('"', "'", $this->default).'"';
		}
		$content .= ', nullable='.($this->nullable ? 'true' : 'false').')'.PHP_EOL;

		$content .= $startLineCharacter.' * @var '.$this->getPhpType().PHP_EOL;
		$content .= $startLineCharacter.' */'.PHP_EOL;
		$content .= $startLineCharacter.'protected $'.$this->name.';'.PHP_EOL;

		return $content;
	}
}