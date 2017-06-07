<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractExpeditor extends AbstractModel {

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
	public $name;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $amountVatExcluded;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

}