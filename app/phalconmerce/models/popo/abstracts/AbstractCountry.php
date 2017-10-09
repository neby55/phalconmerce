<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractCountry extends AbstractModel {
	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=3, nullable=false)
	 * @var string
	 */
	public $fk_currency_id;

	/**
	 * @Column(type="string", length=2, nullable=true)
	 * @var string
	 */
	public $isoCode2;

	/**
	 * @Column(type="string", length=3, nullable=true)
	 * @var string
	 */
	public $isoCode3;

	/**
	 * @Column(type="string", length=64, nullable=false)
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}