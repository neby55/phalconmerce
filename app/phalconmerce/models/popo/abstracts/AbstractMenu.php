<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;


abstract class AbstractMenu extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_menu_group_id;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_url_id;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_menu_id;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_lang_id;

	/**
	 * @Column(type="string", length=64, nullable=false)
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $externalUrl;

	/**
	 * @Column(type="integer", length=4, nullable=false, default=999)
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
		return \Phalconmerce\Models\Popo\Menu::findFirst($this->getParentId());
	}

	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->fk_menu_id;
	}

}