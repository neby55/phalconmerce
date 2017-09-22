<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models;

use Phalcon\Di;

class Design {
	/** @var string */
	protected $slug;

	/** @var string */
	protected $name;

	/** @var DesignParam[] */
	protected $params;

	public function __construct($slug='', $name='', $params=null) {
		$this->slug = $slug;
		$this->name = $name;
		$this->params = $params;
	}

	protected static function getDesignsDirectory() {
		return APP_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.Di::getDefault()->get('config')->frontTheme.DIRECTORY_SEPARATOR.'designs';
	}

	/**
	 * @return mixed
	 */
	protected static function getCache() {
		// Cache for 1 day
		$frontCache = new \Phalcon\Cache\Frontend\Data(
			[
				"lifetime" => 86400,
			]
		);
		$cache = new \Phalcon\Cache\Backend\File(
			$frontCache,
			[
				"cacheDir" => Di::getDefault()->get('config')->cacheDir,
			]
		);

		return $cache->get('all_designs');
	}

	/**
	 * @param mixed $data
	 */
	protected static function setCache($data) {
		// Cache for 1 day
		$frontCache = new \Phalcon\Cache\Frontend\Data(
			[
				"lifetime" => 86400,
			]
		);
		$cache = new \Phalcon\Cache\Backend\File(
			$frontCache,
			[
				"cacheDir" => Di::getDefault()->get('config')->cacheDir,
			]
		);

		$cache->save('all_designs', $data);
	}

	/**
	 * @return Design[]
	 */
	public static function loadAllDesigns() {
		$cacheData = self::getCache();

		// If data cached
		if ($cacheData !== null) {
			return $cacheData;
		}
		// If not, get datas, and save it
		else {
			$dir = self::getDesignsDirectory();
			$results = array();
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if (substr($file, -5) == '.json') {
							$currentDesign = self::loadFromFile($file);

							if (!empty($currentDesign)) {
								$results[$currentDesign->getSlug()] = $currentDesign;
							}
						}
					}
					closedir($dh);
				}
			}

			// Save to cache
			self::setCache($results);

			return $results;
		}
	}

	/**
	 * @param string $filename
	 * @return bool|Design
	 */
	public static function loadFromFile($filename) {
		if (substr($filename, -5) != '.json') {
			$slug = $filename;
			$filename .= '.json';
		}
		else {
			$slug = substr($filename, 0, -5);
		}
		$phpFilename = $slug.'.php';

		$absoluteFilename = self::getDesignsDirectory().DIRECTORY_SEPARATOR.$filename;
		$absolutePhpFilename = self::getDesignsDirectory().DIRECTORY_SEPARATOR.$filename;
		if (file_exists($absolutePhpFilename) && file_exists($absoluteFilename)) {
			$json = file_get_contents($absoluteFilename);
			$data = json_decode($json, true);

			if ($data != null && isset($data['name'])) {
				$params = array();
				if (isset($data['params']) && is_array($data['params'])) {
					foreach ($data['params'] as $currentParamName=>$currentParamType) {
						$currentDesignParam = new DesignParam($currentParamName, DesignParam::getTypeByName($currentParamType));
						if ($currentDesignParam->isValid()) {
							$params[$currentDesignParam->getName()] = $currentDesignParam;
						}
					}
				}

				return new Design(
					$slug,
					$data['name'],
					$params
				);
			}
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return DesignParam[]
	 */
	public function getParams() {
		return $this->params;
	}
}