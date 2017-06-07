<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;


abstract class AbstractShipment extends AbstractModel {
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
	public $fk_expeditor_id;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var int
	 */
	public $date;

	/**
	 * @Column(type="string", length=64, nullable=true)
	 * @var string
	 */
	public $number;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

}