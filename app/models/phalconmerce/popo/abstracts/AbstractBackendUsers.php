<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

abstract class AbstractBackendUsers extends AbstractModel {

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
	public $username;

	/**
	 * @Column(type="string", length=255, nullable=false)
	 * @var string
	 */
	public $email;

	/**
	 * @Column(type="string", length=60, nullable=false)
	 * @var string
	 */
	public $hashedPassword;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}
