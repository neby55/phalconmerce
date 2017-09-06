<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractAttributeSet extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

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
}