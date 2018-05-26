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
use Phalconmerce\Models\Popo\Url;

class Breadcrumb {
	/** @var string */
	protected $name;
	/** @var int */
	protected $fk_url_id;
	/** @var string */
	protected $relativeUrl;
	/** @var array */
	protected $options;

	public function __construct($name='', $fk_url_id=0, $relativeUrl='', $options=array()) {
		$this->name = $name;
		$this->fk_url_id = $fk_url_id;
		$this->relativeUrl = $relativeUrl;
		$this->options = $options;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return int
	 */
	public function getFkUrlId() {
		return $this->fk_url_id;
	}

	/**
	 * @param int $fk_url_id
	 */
	public function setFkUrlId($fk_url_id) {
		$this->fk_url_id = $fk_url_id;
	}

	/**
	 * @return string
	 */
	public function getRelativeUrl() {
		return $this->relativeUrl;
	}

	/**
	 * @param string $relativeUrl
	 */
	public function setRelativeUrl($relativeUrl) {
		$this->relativeUrl = $relativeUrl;
	}

	/**
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * @param array $options
	 */
	public function setOptions($options) {
		$this->options = $options;
	}

	/**
	 * @return bool
	 */
	public function isUrlDefined() {
		return ($this->fk_url_id > 0) || (!empty($this->relativeUrl));
	}

	/**
	 * @return string
	 */
	public function getFullUrl() {
		if ($this->fk_url_id > 0) {
			$urlObject = Url::findFirstById($this->fk_url_id);
			if (!empty($urlObject)) {
				return $urlObject->getFullUrl();
			}
		}
		if (!empty($this->relativeUrl)) {
			return $this->relativeUrl;
		}
		return '#';
	}
}