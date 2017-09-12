<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractBackendUser extends AbstractModel {

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
	 * @Column(type="string", length=255, unique=true, nullable=false)
	 * @var string
	 */
	public $email;

	/**
	 * @Column(type="string", length=60, nullable=true, editable=false)
	 * @var string
	 */
	public $hashedPassword;

	/**
	 * @Column(type="string", length=16, nullable=false)
	 * @var string
	 */
	public $role;

	/**
	 * @Column(type="string", length=32, unique=true, nullable=true)
	 * @var string
	 */
	public $token;

	/**
	 * @Column(type="timestamp", nullable=true, default="0000-00-00 00:00:00")
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

	/**
	 * @param string $email
	 * @return static
	 */
	public static function findByEmail($email) {
		return self::find([
			'conditions' => 'email = :email:',
			'bind'       => [
				':email' => $email,
			]
		])->getFirst();
	}
}
