<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractFriendSponsoring extends AbstractModel {

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
	public $fk_voucher_id;

	/**
	 * @Column(type="string", length=128, nullable=false)
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="string", length=255, nullable=false)
	 * @var string
	 * @Index
	 */
	public $email;

	/**
	 * @Column(type="string", length=64, nullable=false)
	 * @var string
	 */
	public $token;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}