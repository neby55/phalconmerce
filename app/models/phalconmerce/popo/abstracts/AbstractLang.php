<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;


abstract class AbstractLang extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="string", length=2, nullable=false)
	 * @Index
	 * @var string
	 */
	public $code;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=99)
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