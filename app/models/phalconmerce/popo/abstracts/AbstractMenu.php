<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;


abstract class AbstractMenu extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_menugroup_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_url_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_menu_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_lang_id;

	/**
	 * @Column(type="string", length=64, nullable=false)
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="string", length=255, nullable=false)
	 * @var string
	 */
	public $externalUrl;

	/**
	 * @Column(type="integer", length=4, nullable=false, default=99)
	 * @var int
	 */
	public $position;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/**
	 * @return AbstractMenu
	 */
	public function getParent() {
		return \Phalconmerce\Popo\Menu::findFirst($this->getParentId());
	}

	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->fk_menu_id;
	}

}