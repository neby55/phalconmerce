<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractCategory extends AbstractModel {

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
	public $fk_category_id;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $type;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @Translate
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/**
	 * @return mixed
	 */
	public function getParent() {
		return \Phalconmerce\Popo\Category::findFirst($this->getParentId());
	}

	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->fk_category_id;
	}
}