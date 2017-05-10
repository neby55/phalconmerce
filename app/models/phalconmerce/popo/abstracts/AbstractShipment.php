<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;


class AbstractShipment extends AbstractModel {
	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

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