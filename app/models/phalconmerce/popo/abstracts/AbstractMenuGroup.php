<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

class AbstractMenuGroup extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=64, nullable=false)
	 * @var string
	 */
	public $name;

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

}