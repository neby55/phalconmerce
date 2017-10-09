<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Models;

class SubMenu {
	/** @var string */
	protected $label;
	/** @var MenuLink */
	protected $menuLink;
	/** @var int */
	protected $status;

	public function __construct($label, $link, $status=1) {
		$this->label = $label;
		$this->status = $status;
		if (is_string($link)) {
			if (substr($link, 0, 7) == 'http://' || substr($link, 0, 8) == 'https://') {
				$this->menuLink = new MenuExternalLink($link);
			}
			else {
				$this->menuLink = new MenuControllerIndexLink($link);
			}
		}
		else if (is_a($link, '\Backend\Models\MenuLink')) {
			$this->menuLink = $link;
		}
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @return MenuLink
	 */
	public function getLink() {
		return $this->menuLink;
	}

	/**
	 * @return bool
	 */
	public function isActive() {
		return $this->menuLink->isActive();
	}

	/**
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}
}