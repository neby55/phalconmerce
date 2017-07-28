<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

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
	 * @Column(type="string", length=60, nullable=false, editable=false)
	 * @var string
	 */
	public $hashedPassword;

	/**
	 * @Column(type="int", length=2, nullable=false)
	 * @var int
	 */
	public $role;

	/**
	 * @Column(type="string", length=32, nullable=true)
	 * @var string
	 */
	public $token;

	/**
	 * @Column(type="timestamp", nullable=true)
	 * @var int
	 */
	public $tokenExpiry;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/**
	 * Array of values for select element on backOffice
	 * @return array
	 */
	public static function roleSelectOptions() {
		return array(
			1 => 'user',
			2 => 'editor',
			3 => 'admin'
		);
	}
}
