<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

class AbstractCustomer extends AbstractModel {
	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

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
	 * @Column(type="string", length=32, nullable=true)
	 * @var string
	 */
	public $company;

	/**
	 * @Column(type="string", length=128, nullable=false, unique=true)
	 * @var string
	 */
	public $email;

	/**
	 * @Column(type="string", length=60, nullable=false)
	 * @var string
	 */
	public $hashedPassword;

	/**
	 * @Column(type="string", length=20, nullable=true)
	 * @var string
	 */
	public $phoneNumber;

	/**
	 * @Column(type="date", nullable=true)
	 * @var string
	 */
	public $birthDate;

	/**
	 * @Column(type="string", length=60, nullable=false)
	 * @var string
	 */
	public $firstLine;

	/**
	 * @Column(type="string", length=60, nullable=true)
	 * @var string
	 */
	public $secondLine;

	/**
	 * @Column(type="string", length=60, nullable=true)
	 * @var string
	 */
	public $thirdLine;

	/**
	 * @Column(type="string", length=20, nullable=false)
	 * @var string
	 */
	public $zipCode;

	/**
	 * @Column(type="string", length=60, nullable=false)
	 * @var string
	 */
	public $city;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_state_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_country_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_lang_id;
}