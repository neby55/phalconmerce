<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon Developer Tools                                                |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2016 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

namespace Phalconmerce\Services;

use Phalcon\Db;
use Phalcon\Text;
use Phalcon\Db\Column;
///use Phalcon\Generator\Snippet;
//use Phalcon\Mvc\Model\Migration\Profiler;
use Phalcon\Db\Exception as DbException;
use Phalcon\Events\Manager as EventsManager;
use Phalconmerce\Utils;
//use Phalcon\Version\Item as VersionItem;
use DirectoryIterator;

/**
 * Phalcon\Mvc\Model\Migration
 *
 * Migrations of DML y DDL over databases
 *
 * @package Phalcon\Mvc\Model
 */
class TableGenerator {

	/**
	 * Migration database connection
	 * @var \Phalcon\Db\AdapterInterface
	 */
	protected static $_connection;

	/**
	 * Database configuration
	 * @var \Phalcon\Config
	 */
	private static $_databaseConfig;

	/**
	 * Path where to save the migration
	 * @var string
	 */
	private static $_migrationPath = null;

	/**
	 * Skip auto increment
	 * @var bool
	 */
	private static $_skipAI = false;

	/**
	 * Version of the migration file
	 *
	 * @var string
	 */
	protected $_version = null;

	/**
	 * Prepares component
	 *
	 * @param \Phalcon\Config $database Database config
	 *
	 * @throws \Phalcon\Db\Exception
	 */
	public static function setup($database) {
		if (!isset($database->adapter)) {
			throw new DbException('Unspecified database Adapter in your configuration!');
		}

		$adapter = '\\Phalcon\\Db\\Adapter\\Pdo\\' . $database->adapter;

		if (!class_exists($adapter)) {
			throw new DbException('Invalid database Adapter!');
		}

		$configArray = $database->toArray();
		unset($configArray['adapter']);
		self::$_connection = new $adapter($configArray);
		self::$_databaseConfig = $database;

		if ($database->adapter == 'Mysql') {
			self::$_connection->query('SET FOREIGN_KEY_CHECKS=0');
		}
	}

	/**
	 * Set the skip auto increment value
	 *
	 * @param string $skip
	 */
	public static function setSkipAutoIncrement($skip) {
		self::$_skipAI = $skip;
	}

	/**
	 * Returns database name
	 *
	 * @return mixed
	 */
	public static function getDbName() {
		return self::$_databaseConfig->get('dbname');
	}

	/**
	 * Look for table definition modifications and apply to real table
	 *
	 * @param $tableName
	 * @param $definition
	 *
	 * @throws \Phalcon\Db\Exception
	 */
	public function morphTable($tableName, $definition) {
		$defaultSchema = Utils::resolveDbSchema(self::$_databaseConfig);
		$tableExists = self::$_connection->tableExists($tableName, $defaultSchema);

		if (isset($definition['columns'])) {
			if (count($definition['columns']) == 0) {
				throw new DbException('Table must have at least one column');
			}

			$fields = array();
			foreach ($definition['columns'] as $tableColumn) {
				if (!is_object($tableColumn)) {
					throw new DbException('Table must have at least one column');
				}
				/**
				 * @var \Phalcon\Db\ColumnInterface $tableColumn
				 * @var \Phalcon\Db\ColumnInterface[] $fields
				 */
				$fields[$tableColumn->getName()] = $tableColumn;
			}

			if ($tableExists == true) {
				$localFields = array();
				/**
				 * @var \Phalcon\Db\ColumnInterface[] $description
				 * @var \Phalcon\Db\ColumnInterface[] $localFields
				 */
				$description = self::$_connection->describeColumns($tableName, $defaultSchema);
				foreach ($description as $field) {
					$localFields[$field->getName()] = $field;
				}

				foreach ($fields as $fieldName => $tableColumn) {
					/**
					 * @var \Phalcon\Db\ColumnInterface $tableColumn
					 * @var \Phalcon\Db\ColumnInterface[] $localFields
					 */
					if (!isset($localFields[$fieldName])) {
						self::$_connection->addColumn($tableName, $tableColumn->getSchemaName(), $tableColumn);
					}
					else {
						$changed = false;

						if ($localFields[$fieldName]->getType() != $tableColumn->getType()) {
							$changed = true;
						}

						if ($localFields[$fieldName]->getSize() != $tableColumn->getSize()) {
							$changed = true;
						}

						if ($tableColumn->isNotNull() != $localFields[$fieldName]->isNotNull()) {
							$changed = true;
						}

						if ($tableColumn->getDefault() != $localFields[$fieldName]->getDefault()) {
							$changed = true;
						}

						if ($changed == true) {
							self::$_connection->modifyColumn($tableName, $tableColumn->getSchemaName(), $tableColumn, $tableColumn);
						}
					}
				}

				foreach ($localFields as $fieldName => $localField) {
					if (!isset($fields[$fieldName])) {
						self::$_connection->dropColumn($tableName, null, $fieldName);
					}
				}
			}
			else {
				self::$_connection->createTable($tableName, $defaultSchema, $definition);
				if (method_exists($this, 'afterCreateTable')) {
					$this->afterCreateTable();
				}
			}
		}

		if (isset($definition['references'])) {
			if ($tableExists == true) {
				$references = array();
				foreach ($definition['references'] as $tableReference) {
					$references[$tableReference->getName()] = $tableReference;
				}

				$localReferences = array();
				$activeReferences = self::$_connection->describeReferences($tableName, $defaultSchema);
				foreach ($activeReferences as $activeReference) {
					$localReferences[$activeReference->getName()] = array(
						'referencedTable' => $activeReference->getReferencedTable(),
						'columns' => $activeReference->getColumns(),
						'referencedColumns' => $activeReference->getReferencedColumns(),
					);
				}

				foreach ($definition['references'] as $tableReference) {
					if (!isset($localReferences[$tableReference->getName()])) {
						self::$_connection->addForeignKey($tableName, $tableReference->getSchemaName(), $tableReference);
					}
					else {
						$changed = false;
						if ($tableReference->getReferencedTable() != $localReferences[$tableReference->getName()]['referencedTable']) {
							$changed = true;
						}

						if ($changed == false) {
							if (count($tableReference->getColumns()) != count($localReferences[$tableReference->getName()]['columns'])) {
								$changed = true;
							}
						}

						if ($changed == false) {
							if (count($tableReference->getReferencedColumns()) != count($localReferences[$tableReference->getName()]['referencedColumns'])) {
								$changed = true;
							}
						}
						if ($changed == false) {
							foreach ($tableReference->getColumns() as $columnName) {
								if (!in_array($columnName, $localReferences[$tableReference->getName()]['columns'])) {
									$changed = true;
									break;
								}
							}
						}
						if ($changed == false) {
							foreach ($tableReference->getReferencedColumns() as $columnName) {
								if (!in_array($columnName, $localReferences[$tableReference->getName()]['referencedColumns'])) {
									$changed = true;
									break;
								}
							}
						}

						if ($changed == true) {
							self::$_connection->dropForeignKey($tableName, $tableReference->getSchemaName(), $tableReference->getName());
							self::$_connection->addForeignKey($tableName, $tableReference->getSchemaName(), $tableReference);
						}
					}
				}

				foreach ($localReferences as $referenceName => $reference) {
					if (!isset($references[$referenceName])) {
						self::$_connection->dropForeignKey($tableName, null, $referenceName);
					}
				}
			}
		}

		if (isset($definition['indexes'])) {
			if ($tableExists == true) {
				$indexes = array();
				foreach ($definition['indexes'] as $tableIndex) {
					$indexes[$tableIndex->getName()] = $tableIndex;
				}

				$localIndexes = array();
				$actualIndexes = self::$_connection->describeIndexes($tableName, $defaultSchema);
				foreach ($actualIndexes as $actualIndex) {
					$localIndexes[$actualIndex->getName()] = $actualIndex->getColumns();
				}

				foreach ($definition['indexes'] as $tableIndex) {
					if (!isset($localIndexes[$tableIndex->getName()])) {
						if ($tableIndex->getName() == 'PRIMARY') {
							self::$_connection->addPrimaryKey($tableName, $tableColumn->getSchemaName(), $tableIndex);
						}
						else {
							self::$_connection->addIndex($tableName, $tableColumn->getSchemaName(), $tableIndex);
						}
					}
					else {
						$changed = false;
						if (count($tableIndex->getColumns()) != count($localIndexes[$tableIndex->getName()])) {
							$changed = true;
						}
						else {
							foreach ($tableIndex->getColumns() as $columnName) {
								if (!in_array($columnName, $localIndexes[$tableIndex->getName()])) {
									$changed = true;
									break;
								}
							}
						}
						if ($changed == true) {
							if ($tableIndex->getName() == 'PRIMARY') {
								self::$_connection->dropPrimaryKey($tableName, $tableColumn->getSchemaName());
								self::$_connection->addPrimaryKey($tableName, $tableColumn->getSchemaName(), $tableIndex);
							}
							else {
								self::$_connection->dropIndex($tableName, $tableColumn->getSchemaName(), $tableIndex->getName());
								self::$_connection->addIndex($tableName, $tableColumn->getSchemaName(), $tableIndex);
							}
						}
					}
				}
				foreach ($localIndexes as $indexName => $indexColumns) {
					if (!isset($indexes[$indexName])) {
						self::$_connection->dropIndex($tableName, null, $indexName);
					}
				}
			}
		}
	}

	/**
	 * @param string $tableName
	 * @return bool
	 */
	public function dropTable($tableName) {
		return self::$_connection->dropTable($tableName);
	}
}
