<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

abstract class AbstractVouncher extends AbstractModel {

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
	 * @Index
	 */
	public $code;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $type;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $startDate;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $endDate;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $useDate;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $amountVatExcluded;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $amountMinimumVatExcluded;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}