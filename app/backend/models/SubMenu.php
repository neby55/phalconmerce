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

	public function __construct($label, $link) {
		$this->label = $label;
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
}