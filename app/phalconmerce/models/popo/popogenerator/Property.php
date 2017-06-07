<?php

namespace Phalconmerce\Models\Popo\Popogenerator;

use Phalconmerce\Models\Utils;

class Property {
	/** @var string */
	protected $name;
	/** @var int */
	protected $type;
	/** @var int */
	protected $length;
	/** @var boolean */
	protected $unsigned;
	/** @var boolean */
	protected $nullable;
	/** @var boolean */
	protected $unique;
	/** @var string */
	protected $default;
	/** @var boolean */
	protected $translate;

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
	 * @return mixed|string
	 */
	public function getForeignKeyClassName() {
		if ($this->isForeignKey()) {
			$tmp = explode(self::FK_SEPARATOR, $this->name);
			if (sizeof($tmp) >= 3) {
				return Utils::getClassNameFromTableName($tmp[1]);
			}
			else {
				throw new \InvalidArgumentException('ForeignKey properties should follow this pattern : fk_tablename_idproperty');
			}
		}
		return false;
	}

	/**
	 * @return mixed|string
	 */
	public function getForeignKeyPropertyName() {
		if ($this->isForeignKey()) {
			$tmp = explode(self::FK_SEPARATOR, $this->name);
			if (sizeof($tmp) >= 3) {
				$tableName = $tmp[1];
				unset($tmp[0]);
				unset($tmp[1]);
				return join(self::FK_SEPARATOR, $tmp);
			}
			else {
				throw new \InvalidArgumentException('ForeignKey properties should follow this pattern : fk_tablename_idproperty');
			}
		}
		return false;
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

		if ($this->isTranslate()) {
			$content .= $startLineCharacter . ' * @Translate' . PHP_EOL;
		}
		$content .= $startLineCharacter.' * @var '.$this->getPhpType().PHP_EOL;
		$content .= $startLineCharacter.' */'.PHP_EOL;
		$content .= $startLineCharacter.'public $'.$this->name.';'.PHP_EOL;

		return $content;
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
	 * @return int
	 */
	public function getLength() {
		return $this->length;
	}

	/**
	 * @return boolean
	 */
	public function isUnsigned() {
		return $this->unsigned;
	}

	/**
	 * @return boolean
	 */
	public function isNullable() {
		return $this->nullable;
	}

	/**
	 * @return boolean
	 */
	public function isUnique() {
		return $this->unique;
	}

	/**
	 * @return string
	 */
	public function getDefault() {
		return $this->default;
	}

	/**
	 * @param int $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @param int $length
	 */
	public function setLength($length) {
		$this->length = $length;
	}

	/**
	 * @param boolean $unsigned
	 */
	public function setUnsigned($unsigned) {
		$this->unsigned = $unsigned;
	}

	/**
	 * @param boolean $nullable
	 */
	public function setNullable($nullable) {
		$this->nullable = $nullable;
	}

	/**
	 * @param boolean $unique
	 */
	public function setUnique($unique) {
		$this->unique = $unique;
	}

	/**
	 * @param string $default
	 */
	public function setDefault($default) {
		$this->default = $default;
	}

	/**
	 * @return boolean
	 */
	public function isTranslate() {
		return $this->translate;
	}

	/**
	 * @param boolean $translate
	 */
	public function setTranslate($translate) {
		$this->translate = $translate;
	}
}