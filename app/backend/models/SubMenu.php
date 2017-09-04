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
	protected $link;

	public function __construct($label, $link) {
		$this->label = $label;
		if (is_string($link)) {
			$this->link = new MenuLink($link);
		}
		else if (is_a($link, '\Backend\Models\MenuLink')) {
			$this->link = $link;
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
		return $this->link;
	}
}