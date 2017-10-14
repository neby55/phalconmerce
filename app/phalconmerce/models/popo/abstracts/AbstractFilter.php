<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractFilter extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=16, nullable=false)
	 * @Index
	 * @var string
	 */
	public $slug;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @Index
	 * @var int
	 */
	public $type;

	/**
	 * @Column(type="integer", length=2, nullable=true, default=99)
	 * @Index
	 * @var int
	 */
	public $position;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}