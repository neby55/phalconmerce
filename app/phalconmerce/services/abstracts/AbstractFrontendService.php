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
use Phalconmerce\Models\Popo\Url;
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

	const PROJECT_NAME = 'Phalconmerce';
	const DEFAULT_LANG_ID = 2;
	const COOKIE_LANG_NAME = 'lang';
	const COOKIE_CURRENCY_NAME = 'currency';
	const COOKIES_LIFETIME_IN_DAYS = 365;

	public function __construct() {
		// TODO retriveve infos to complete properties
		$this->baseURL = Di::getDefault()->get('url')->getBaseUri();
		$this->currentURL = Di::getDefault()->get('router')->getRewriteUri();
		$this->absoluteURL = (isset($_SERVER['https']) ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].$this->currentURL;
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
	 * @param float $price
	 * @return string
	 */
	public function formatPriceVatInc($price) {
		return $this->formatPrice($price * $this->getVatRatio());
	}

	/**
	 * @param float $price
	 * @return string
	 */
	public function formatPriceVatExc($price) {
		return $this->formatPrice($price);
	}

	/**
	 * @param float $price
	 * @return string
	 */
	public function formatPrice($price) {
		// If decimals
		if (floor($price) != $price) {
			return number_format($price, 2, ',', ' ').' '.$this->getDI()->get('translation')->getCurrencySigle();
		}
		else {
			return $price.' '.$this->getDI()->get('translation')->getCurrencySigle();
		}
	}

	/**
	 * @param string $entity
	 * @param int $entityId
	 * @return string
	 */
	public function getEntityPermalink($entity, $entityId) {
		$object = Url::findFirst(array(
			'entity = :entity: AND entityId = :entity_id: AND fk_lang_id = :fk_lang_id:',
			'bind' => array(
				'entity' => $entity,
				'entity_id' => $entityId,
				'fk_lang_id' => $this->getDI()->get('translation')->getLangId()
			)
		));
		if (!empty($object)) {
			return Di::getDefault()->get('url')->getBaseUri().$object->permalink;
		}
		return false;
	}

	/**
	 * @return float
	 */
	public function getVatRatio() {
		return 1.2;
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