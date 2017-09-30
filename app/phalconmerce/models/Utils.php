<?php

namespace Phalconmerce\Models;

use Phalcon\Config;
use Phalcon\Di;

class Utils {
	const DB_ADAPTER_POSTGRESQL = 'Postgresql';

	const DB_ADAPTER_SQLITE = 'Sqlite';

	/**
	 * Converts the underscore_notation to the UpperCamelCase
	 *
	 * @param string $string
	 * @return string
	 */
	public static function camelize($string) {
		$stringParts = explode('_', $string);
		$stringParts = array_map('ucfirst', $stringParts);

		return implode('', $stringParts);
	}

	/**
	 * Converts the underscore_notation to the lowerCamelCase
	 *
	 * @param string $string
	 * @return string
	 */
	public static function lowerCamelize($string) {
		return lcfirst(self::camelize($string));
	}

	/**
	 * Resolves the DB Schema
	 *
	 * @param \Phalcon\Config $config
	 * @return null|string
	 */
	public static function resolveDbSchema(Config $config) {
		if ($config->offsetExists('schema')) {
			return $config->get('schema');
		}

		if (self::DB_ADAPTER_POSTGRESQL == $config->get('adapter')) {
			return 'public';
		}

		if (self::DB_ADAPTER_SQLITE == $config->get('adapter')) {
			// SQLite only supports the current database, unless one is
			// attached. This is not the case, so don't return a schema.
			return null;
		}

		if ($config->offsetExists('dbname')) {
			return $config->get('dbname');
		}

		return null;
	}

	/**
	 * @param string $className
	 * @return string
	 */
	public static function getTableNameFromClassName($className) {
		$tableName = $className;

		if (substr($tableName, 0, 8) == 'Abstract') {
			$tableName = substr($tableName, 8);
		}

		$tableName = ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $tableName)), '_');

		// If nmTable with '_has_'
		if (strpos($tableName, '_has_') !== false) {
			list($firstTable,$secondTable) = explode('_has_', $tableName);
			$tableName = self::getTableNameFromClassName($firstTable).'_has_'.$secondTable;
		}

		return $tableName;
	}

	/**
	 * @param string $tableName
	 * @return mixed|string
	 */
	public static function getClassNameFromTableName($tableName) {
		$className = $tableName;

		$className = '_'.$className;
		$className = str_replace(array('_', ' '), '', (ucfirst(preg_replace_callback(
			'/_[a-zA-Z]/',
			function($match) {
				return strtoupper($match[0]);
			},
			$className)
		)));

		return $className;
	}

	/**
	 * @param string $fqcn
	 * @return string
	 */
	public static function getClassnameFromFQCN($fqcn) {
		return substr($fqcn, strrpos($fqcn, '\\')+1);
	}

	/**
	 * @param string $filename
	 * @return string
	 */
	private static function getDataFullFilename($filename) {
		if (substr($filename, -4) != '.php') {
			$filename .= '.php';
		}
		return Di::getDefault()->getShared('configPhalconmerce')->cacheDir.DIRECTORY_SEPARATOR.$filename;
	}

	/**
	 * @param mixed $data
	 * @param string $filename
	 * @return bool
	 */
	public static function saveData($data, $filename) {
		$fp = fopen(self::getDataFullFilename($filename), 'w');
		if ($fp) {
			fputs($fp, serialize($data));
			fclose($fp);

			return true;
		}
		return false;
	}

	/**
	 * @param string $filename
	 * @return bool
	 */
	public static function loadData($filename) {
		if (file_exists(self::getDataFullFilename($filename))) {
			$content = file_get_contents(self::getDataFullFilename($filename));
			if (!empty($content)) {
				return unserialize($content);
			}
		}
		return false;
	}

	/**
	 * @param mixed $var
	 */
	public static function debug($var) {
		if (is_a($var, '\Phalcon\Mvc\Model')) {
			echo '<pre style="background: black;color:white;padding:8px 10px;">'.get_class($var).PHP_EOL.print_r($var->toArray(),1).'</pre>';
		}
		else {
			echo '<pre style="background: black;color:white;padding:8px 10px;">'.print_r($var,1).'</pre>';
		}
	}
}
