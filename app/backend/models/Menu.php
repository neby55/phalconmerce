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
	/** @var int */
	protected $status;

	const STATUS_ACTIVE = 1;
	const STATUS_NEW = 2;
	const STATUS_IN_PROGRESS = 3;
	const STATUS_DEPRECATED = 4;

	public function __construct($label, $icon, $link=null, $status=1) {
		$this->label = $label;
		$this->icon = $icon;
		$this->status = $status;
		if (is_string($link)) {
			if (substr($link, 0, 7) == 'http://' || substr($link, 0, 8) == 'https://') {
				$this->link = new MenuExternalLink($link);
			}
			else {
				$this->link = new MenuControllerIndexLink($link);
			}
		}
		else if (is_a($link, '\Backend\Models\MenuLink')) {
			$this->link = $link;
		}
		$this->subMenuList = array();
	}

	/**
	 * @return bool
	 */
	public function isActive() {
		if ($this->hasSubMenu()) {
			foreach ($this->getSubMenuList() as $currentSubMenu) {
				if ($currentSubMenu->isActive()) {
					return true;
				}
			}
		}
		else {
			return $this->link->isActive();
		}
		return false;
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

	/**
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}
}