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
	/** @var string */
	protected $help;
	/** @var DesignParam[] */
	protected $params;

	const VIEWS_DIRECTORY = '_designs';

	public function __construct($slug='', $name='', $help='', $params=null) {
		$this->slug = $slug;
		$this->name = $name;
		$this->help = $help;
		$this->params = $params;
	}

	protected static function getDesignsDirectory() {
		return APP_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.Di::getDefault()->get('config')->frontTheme.DIRECTORY_SEPARATOR.self::VIEWS_DIRECTORY;
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
	 * @return Design
	 */
	public static function loadFromFile($filename) {
		if (substr($filename, -5) != '.json') {
			$slug = $filename;
			$filename .= '.json';
		}
		else {
			$slug = substr($filename, 0, -5);
		}
		$phpFilename = $slug.'.phtml';

		$absoluteFilename = self::getDesignsDirectory().DIRECTORY_SEPARATOR.$filename;
		$absolutePhpFilename = self::getDesignsDirectory().DIRECTORY_SEPARATOR.$filename;
		if (file_exists($absolutePhpFilename) && file_exists($absoluteFilename)) {
			$json = file_get_contents($absoluteFilename);
			$data = json_decode($json, true);

			if ($data != null && isset($data['name'])) {
				$params = array();
				if (isset($data['params']) && is_array($data['params'])) {
					foreach ($data['params'] as $currentParamName=>$currentParamInfos) {
						if (is_array($currentParamInfos)) {
							$currentParamType = $currentParamInfos['type'];
							$currentParamLabel = $currentParamInfos['label'];
						}
						else {
							$currentParamType = $currentParamInfos;
							$currentParamLabel = '';
						}
						$currentDesignParam = new DesignParam($currentParamName, $currentParamLabel, DesignParam::getTypeByName($currentParamType));
						if ($currentDesignParam->isValid()) {
							$params[$currentDesignParam->getName()] = $currentDesignParam;
						}
					}
				}
				$helpText = '';
				if (isset($data['help'])) {
					if (!is_array($data['help'])) {
						$data['help'] = array($data['help']);
					}
					$helpText = join('<br>', $data['help']);
				}

				return new Design(
					$slug,
					$data['name'],
					$helpText,
					$params
				);
			}
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function hasScreenshot() {
		$absoluteFilename = self::getDesignsDirectory().DIRECTORY_SEPARATOR.$this->getSlug().'.png';
		return file_exists($absoluteFilename);
	}

	/**
	 * @return string
	 */
	public function getScreenshotBase64Source() {
		$absoluteFilename = self::getDesignsDirectory().DIRECTORY_SEPARATOR.$this->getSlug().'.png';
		if (file_exists($absoluteFilename)) {
			return base64_encode(file_get_contents($absoluteFilename));
		}
		return '';
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

	/**
	 * @return string
	 */
	public function getHelp() {
		return $this->help;
	}

	/**
	 * @return string
	 */
	public function getViewPick() {
		return self::VIEWS_DIRECTORY.'/'.$this->slug;
	}
}