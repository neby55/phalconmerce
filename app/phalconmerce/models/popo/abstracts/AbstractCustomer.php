<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;
use Phalconmerce\Models\Popo\NewsletterSignup;

abstract class AbstractCustomer extends AbstractModel {
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
	public $fk_lang_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_address_id;

	/**
	 * @Column(type="string", length=10, nullable=true)
	 * @var string
	 */
	public $gender;

	/**
	 * @Column(type="string", length=32, nullable=true)
	 * @var string
	 */
	public $lastName;

	/**
	 * @Column(type="string", length=32, nullable=true)
	 * @var string
	 */
	public $firstName;

	/**
	 * @Column(type="string", length=128, nullable=false)
	 * @Index
	 * @var string
	 */
	public $email;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @Index
	 * @var string
	 */
	public $emailValidationToken;

	/**
	 * @Column(type="date", nullable=true)
	 * @var string
	 */
	public $emailValidationDate;

	/**
	 * @Column(type="string", length=60, nullable=true)
	 * @var string
	 */
	public $hashedPassword;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @Index
	 * @var string
	 */
	public $resetPasswordToken;

	/**
	 * @Column(type="timestamp", nullable=true, default='0000-00-00 00:00:00')
	 * @var string
	 */
	public $resetPasswordDate;

	/**
	 * @Column(type="date", nullable=true)
	 * @var string
	 */
	public $birthDate;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	public function setNewEmailValidationToken() {
		if ($this->id > 0) {
			$this->emailValidationToken = md5(mt_rand(4, 785) . $this->email . 'Phalcon' . getmypid() . 'merce');
			$this->save();
		}
	}

	public function setNewResetPasswordToken() {
		if ($this->id > 0) {
			$this->resetPasswordToken = md5(mt_rand(4, 785) . $this->email . 'Phalcon' . getmypid() . 'merce');
			$this->resetPasswordDate = date('Y-m-d H:i:s');
			$this->save();
		}
	}

	public function isNewsletterActive() {
		$result = NewsletterSignup::findFirstByEmail($this->email);
		if (!empty($result)) {
			return $result->status == 1;
		}
		return false;
	}
}