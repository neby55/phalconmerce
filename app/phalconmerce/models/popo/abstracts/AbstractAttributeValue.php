<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractAttributeValue extends AbstractModel {

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
	public $fk_attribute_id;

	/**
	 * @Column(type="string", length=64, nullable=false)
	 * @Translate
	 * @var string
	 */
	public $value;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/**
	 * @return mixed
	 */
	public function getAttribute() {
		return \Phalconmerce\Models\Popo\Attribute::findFirst($this->getAttributeId());
	}

	/**
	 * @return int
	 */
	public function getAttributeId() {
		return $this->fk_attribute_id;
	}
}