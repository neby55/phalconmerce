<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

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
	 * @Column(type="timestamp", nullable=true)
	 * @var string
	 */
	public $startDate;

	/**
	 * @Column(type="timestamp", nullable=true)
	 * @var string
	 */
	public $endDate;

	/**
	 * @Column(type="timestamp", nullable=true)
	 * @var string
	 */
	public $useDate;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $amountVatExcluded;

	/**
	 * @Column(type="float", nullable=false, default=1)
	 * @var float
	 */
	public $minimumAmountVatExcluded;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}