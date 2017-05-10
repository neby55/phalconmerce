<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

class AbstractBackendUsers extends AbstractModel {
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
	public $password_hash;

	/**
	 * @Column(type="integer", length=2, nullable=false)
	 * @var int
	 */
	public $status;

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getPasswordHash() {
		return $this->password_hash;
	}

	/**
	 * @param string $password_hash
	 */
	public function setPasswordHash($password_hash) {
		$this->password_hash = $password_hash;
	}

	/**
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param int $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}
}
