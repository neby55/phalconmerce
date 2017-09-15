<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 08/02/2017
 * Time: 09:46
 */

namespace Phalconmerce\Models\Popo\Generators\Db;

use Phalcon\Annotations\Collection;
use Phalcon\Db\Column;
use Phalcon\Db\Index as Index;
use Phalcon\Db\Reference as Reference;
use Phalcon\DI;
use Phalconmerce\Models\Utils;
use Phalconmerce\Services\TableGenerator;

class Table {
	/** @var string */
	protected $tableName;
	/** @var Column[] */
	protected $columnList;
	/** @var Index[] */
	protected $indexesList;
	/** @var Reference[] */
	protected $referencesList;
	/** @var string[] */
	protected $primaryKeysList;
	/** @var string */
	protected $lastPropertyName;

	public static $excludedPropertyNamesList = array(
		'prefix'
	);

	const DECIMAL_SIZE = 16;
	const DECIMAL_SCALE = 2;
	const TABLES_TYPE = 'BASE TABLE';
	const TABLES_ENGINE = 'InnoDB';
	const TABLES_CHARSET = 'utf8_general_ci';
	const FK_WORD_SEPARATOR = '_';

	public function __construct($tableName) {
		$this->tableName = $tableName;
		$this->columnList = array();
		$this->referencesList = array();
		$this->indexesList = array();
		$this->primaryKeysList = array();
		$this->lastPropertyName = '';
	}

	/**
	 * @param Column $column
	 */
	public function addColumn(Column $column) {
		$this->columnList[$column->getName()] = $column;
	}

	/**
	 * @param Index $index
	 */
	public function addIndex(Index $index) {
		$this->indexesList[$index->getName()] = $index;
	}

	/**
	 * @param Reference $reference
	 */
	public function addReference(Reference $reference) {
		$this->referencesList[$reference->getName()] = $reference;
	}

	/**
	 * @param string $propertyName
	 */
	public function addPrimaryKey($propertyName) {
		$this->primaryKeysList[$propertyName] = $propertyName;
	}

	/**
	 * @param string $propertyName
	 * @param Collection $collection
	 * @return bool
	 */
	public function addByAnnotations($propertyName, Collection $collection) {
		if ($propertyName != '') {
			// foreign key exception
			if (substr($propertyName, 0, 3) == 'fk'.self::FK_WORD_SEPARATOR) {
				if (strpos($propertyName, self::FK_WORD_SEPARATOR) === false) {
					throw new \InvalidArgumentException('Foreign Key properies must follow this pattern : fk_tablename_idpropertyname'.PHP_EOL.'For example : fk_product_id');
				}
				else {
					$tmp = explode(self::FK_WORD_SEPARATOR, $propertyName);
					if (sizeof($tmp) != 3) {
						throw new \InvalidArgumentException('Foreign Key properies must follow this pattern : fk_tablename_idpropertyname'.PHP_EOL.'For example : fk_product_id');
					}
					$fkData = array(
						'tableName' => $tmp[1],
						'className' => Utils::getClassNameFromTableName($tmp[1]),
						'propertyName' => $tmp[2]
					);

					$columnName = $propertyName;
				}

			}
			else {
				$columnName = $propertyName;
			}

			if ($collection->has('Column')) {
				$columnCollection = $collection->get('Column');
				if ($columnCollection->hasArgument('type')) {
					$columnOptions = array();
					$length = 0;
					if ($columnCollection->hasArgument('length')) {
						$length = $columnCollection->getArgument('length');
					}
					$columnOptions['type'] = self::getColmunTypeByAnnotationType($columnCollection->getArgument('type'), $length);

					// Unsigned
					if ($columnOptions['type'] == Column::TYPE_INTEGER) {
						if ($columnCollection->hasArgument('unsigned') && $columnCollection->getArgument('unsigned') === false) {
							$columnOptions['unsigned'] = false;
						}
						else {
							$columnOptions['unsigned'] = true;
						}
					}
					// Size & scale
					if ($columnOptions['type'] == Column::TYPE_VARCHAR) {
						$columnOptions['size'] = $length;
					}
					if ($columnOptions['type'] == Column::TYPE_DECIMAL) {
						$columnOptions['size'] = self::DECIMAL_SIZE;
						$columnOptions['scale'] = self::DECIMAL_SCALE;
					}
					// Nullable
					if ($columnCollection->hasArgument('nullable') && $columnCollection->getArgument('nullable') === false) {
						$columnOptions['notNull'] = true;
					}
					else {
						$columnOptions['notNull'] = false;
					}
					// AutoIncrement
					if ($columnOptions['type'] == Column::TYPE_INTEGER || $columnOptions['type'] == Column::TYPE_BIGINTEGER) {
						if ($collection->has('Primary')) {
							// If auto increment
							if ($collection->has('Identity')) {
								$columnOptions['autoIncrement'] = true;
								// Force unsigned
								$columnOptions['unsigned'] = true;
							}
						}
					}
					// first or after
					if ($this->lastPropertyName != '') {
						$columnOptions['after'] = $this->lastPropertyName;
					}
					else {
						$columnOptions['first'] = true;
					}
					// default value
					if ($columnCollection->hasArgument('default')) {
						$columnOptions['default'] = $columnCollection->getArgument('default');
					}
					// Timestamp default value exception to avoid CURRENT_TIMESTAMP limitation to only one column
					else if ($columnOptions['type'] == Column::TYPE_TIMESTAMP) {
						$columnOptions['default'] = '0000-00-00 00:00:00';
					}
					// unique
					if ($columnCollection->hasArgument('unique') && $columnCollection->getArgument('unique') == 'true') {
						$this->addIndex(new Index(
							$columnName.'Unique',
							[$columnName],
							'UNIQUE'
						));
					}
					// index for status/active fields
					if ($propertyName == 'status' || $propertyName == 'active') {
						$this->addIndex(new Index(
							$columnName.'Index',
							[$columnName]
						));
					}
					// primary
					if ($collection->has('Primary')) {
						//$columnOptions['primary'] = true;
						$columnOptions['notNull'] = true;
						$this->addPrimaryKey($columnName);
					}
					// index for foreign keys
					else if (!empty($fkData)) {
						$this->addIndex(new Index(
							$columnName.'Index',
							[$columnName]
						));
					}

					// Add columun
					$currentColumn = new Column(
						$columnName,
						$columnOptions
					);
					$this->addColumn($currentColumn);

					// For columns added after
					$this->lastPropertyName = $columnName;

					return true;
				}
			}
		}
		return false;
	}

	/**
	 * @param string $type
	 * @param int $length
	 * @return int
	 */
	private static function getColmunTypeByAnnotationType($type, $length = 0) {
		if ($type == 'integer') {
			// We dont' specify size cause it's only cosmetics
			if (1 <= $length && $length <= 2) {
				// Should be TINYINT, but not implemented in Phalcon yet
				return Column::TYPE_INTEGER;
			}
			else if (3 <= $length && $length <= 4) {
				// Should be SMALLINT, but not implemented in Phalcon yet
				return Column::TYPE_INTEGER;
			}
			else if (5 <= $length && $length <= 11) {
				return Column::TYPE_INTEGER;
			}
			else if (12 <= $length && $length <= 20) {
				return Column::TYPE_BIGINTEGER;
			}
		}
		else if ($type == 'float') {
			return Column::TYPE_DECIMAL;
		}
		else if ($type == 'string') {
			if ($length > 255) {
				return Column::TYPE_TEXT;
			}
			else {
				return Column::TYPE_VARCHAR;
			}
		}
		else if ($type == 'timestamp') {
			return Column::TYPE_TIMESTAMP;
		}
		else if ($type == 'date') {
			return Column::TYPE_DATE;
		}
		else if ($type == 'datetime') {
			return Column::TYPE_DATETIME;
		}
		else if ($type == 'boolean') {
			return Column::TYPE_BOOLEAN;
		}
		else if ($type == 'text') {
			return Column::TYPE_TEXT;
		}
		return 0;
	}

	/**
	 * @throws \Phalcon\Db\Exception
	 */
	public function morph() {
		$generator = new TableGenerator();
		$generator->setup(DI::getDefault()->get('config')->database);

		// Get primary keys and add them
		if (sizeof($this->primaryKeysList) > 0) {
			// Case multiple primary keys => remove auto increment
			if (sizeof($this->primaryKeysList) > 1) {
				foreach ($this->primaryKeysList as $currentPropertyName) {
					if ($this->columnList[$currentPropertyName]->isAutoIncrement()) {
						$originalColumn = $this->columnList[$currentPropertyName];
						$this->columnList[$currentPropertyName] = new Column(
							$originalColumn->getName(),
							[
								'type' => $originalColumn->getBindType(),
								'unsigned' => $originalColumn->isUnsigned(),
								'notNull' => $originalColumn->isNotNull(),
								'after' => $originalColumn->getAfterPosition(),
								'first' => $originalColumn->isFirst()
							]
						);
					}
				}
			}
			// Add primary keys
			$this->addIndex(new Index(
				'PRIMARY',
				$this->primaryKeysList
			));
		}

		// Adding inserted field
		if (!array_key_exists('inserted', $this->columnList)) {
			$this->addColumn(new Column(
				'inserted',
				array(
					'type' => Column::TYPE_TIMESTAMP,
					'notNull' => true,
					'default' => 'CURRENT_TIMESTAMP'
				)
			));
		}
		// Adding updated field
		// TODO faire la partie update => corriger soucis avec default value "On update CURRENT_TIMESTAMP"
		/*if (!array_key_exists('updated', $this->columnList)) {
			$this->addColumn(new Column(
				'updated',
				array(
					'type' => Column::TYPE_TIMESTAMP,
					'notNull' => true,
					'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
				)
			));
		}*/

		try {
			$generator->morphTable($this->tableName,
				[
					'columns' => $this->columnList,
					'indexes' => $this->indexesList,
					'references' => $this->referencesList,
					"options" => [
						"TABLE_TYPE" => self::TABLES_TYPE,
						"ENGINE" => self::TABLES_ENGINE,
						"TABLE_COLLATION" => self::TABLES_CHARSET
					]
				]
			);
		}
		catch (\Phalcon\Db\Exception $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * @return bool
	 * @throws \Phalcon\Db\Exception
	 */
	public function drop() {
		$generator = new TableGenerator();
		$generator->setup(DI::getDefault()->get('config')->database);
		return $generator->dropTable($this->tableName);
	}

	/**
	 * @return string
	 */
	public function getTableName() {
		return $this->tableName;
	}
}