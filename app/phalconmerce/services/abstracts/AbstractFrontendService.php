<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Services\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\Popo\CmsBlock;

abstract class AbstractFrontendService extends MainService {
	/** @var string */
	protected $baseURL;
	/** @var string */
	protected $absoluteURL;
	/** @var string */
	protected $currentURL;
	/** @var string */
	protected $metaTitle;
	/** @var string */
	protected $metaDescription;
	/** @var string */
	protected $metaKeywords;

	const PROJECT_NAME = 'InnocentStone';
	const DEFAULT_LANG_ID = 2;
	const COOKIE_LANG_NAME = 'lang';
	const COOKIE_CURRENCY_NAME = 'currency';
	const COOKIES_LIFETIME_IN_DAYS = 365;

	public function __construct() {
		// TODO retriveve infos to complete properties
		$this->baseURL = '';
		$this->absoluteURL = '';
		$this->currentURL = '';
		$this->metaTitle = '';
		$this->metaDescription = '';
		$this->metaKeywords = '';
	}

	/**
	 * @param string $str
	 * @param array $data
	 * @return string
	 */
	public function e($str, $data=array()) {
		return $this->getDI()->get('translation')->e($str, $data);
	}

	/**
	 * @param string $str
	 * @param array $data
	 * @return string
	 */
	public function t($str, $data=array()) {
		return $this->getDI()->get('translation')->t($str, $data);
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @param int $duration
	 * @return bool
	 */
	public function addCookie($name, $value, $duration) {
		// Handle if $duration is expire timestamp
		$expire = $duration > time() ? $duration : time() + $duration;
		return setcookie($name, $value, $expire, $this->getBaseURL().'/');
	}

	/**
	 * @param string $slug
	 * @return string
	 */
	public function showCmsBlock($slug) {
		$object = CmsBlock::findFirst(array(
			'code = :slug:',
			'bind' => array(
				'slug' => $slug
			)
		));
		if (!empty($object) && is_object($object)) {
			return $object->html;
		}
		return '';
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function addBaseUriIfNeeded($url) {
		// If external URL
		if (preg_match('/^https?:/i', $url) == 1) {
			return $url;
		}
		return $this->getDI()->get('url')->getBaseUri().$url;
	}

	/**
	 * @return string
	 */
	public function getBaseURL() {
		return $this->baseURL;
	}

	/**
	 * @return string
	 */
	public function getCurrentURL() {
		return $this->currentURL;
	}

	/**
	 * @return string
	 */
	public function getAbsoluteURL() {
		return $this->absoluteURL;
	}

	/**
	 * @return string
	 */
	public function getMetaTitle() {
		return $this->metaTitle;
	}

	/**
	 * @param string $metaTitle
	 */
	public function setMetaTitle($metaTitle) {
		$this->metaTitle = $metaTitle;
	}

	/**
	 * @return string
	 */
	public function getMetaDescription() {
		return $this->metaDescription;
	}

	/**
	 * @param string $metaDescription
	 */
	public function setMetaDescription($metaDescription) {
		$this->metaDescription = $metaDescription;
	}

	/**
	 * @return string
	 */
	public function getMetaKeywords() {
		return $this->metaKeywords;
	}

	/**
	 * @param string $metaKeywords
	 */
	public function setMetaKeywords($metaKeywords) {
		$this->metaKeywords = $metaKeywords;
	}

}