<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Backend\Models;

class Menu {
	/** @var string */
	protected $label;
	/** @var string */
	protected $icon;
	/** @var MenuLink */
	protected $link;
	/** @var SubMenu[] */
	protected $subMenuList;

	public function __construct($label, $icon, $link=null) {
		$this->label = $label;
		$this->icon = $icon;
		if (is_string($link)) {
			$this->link = new MenuLink($link);
		}
		else if (is_a($link, '\Backend\Models\MenuLink')) {
			$this->link = $link;
		}
		$this->subMenuList = array();
	}

	/**
	 * @param SubMenu $subMenu
	 */
	public function addSubMenu($subMenu) {
		if (is_a($subMenu, '\Backend\Models\SubMenu')) {
			$this->subMenuList[] = $subMenu;
		}
	}

	/**
	 * @return bool
	 */
	public function hasSubMenu() {
		return sizeof($this->subMenuList) > 0;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @return string
	 */
	public function getIcon() {
		return $this->icon;
	}

	/**
	 * @return MenuLink
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 * @return SubMenu[]
	 */
	public function getSubMenuList() {
		return $this->subMenuList;
	}
}