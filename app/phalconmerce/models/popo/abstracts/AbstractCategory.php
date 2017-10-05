<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractDesignedModel;

abstract class AbstractCategory extends AbstractDesignedModel {

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
	 * @Column(type="integer", length=4, nullable=true, default=999)
	 * @Index
	 * @var int
	 */
	public $position;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/**
	 * @return mixed
	 */
	public function getParent() {
		return \Phalconmerce\Models\Popo\Category::findFirst($this->getParentId());
	}

	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->fk_category_id;
	}
}