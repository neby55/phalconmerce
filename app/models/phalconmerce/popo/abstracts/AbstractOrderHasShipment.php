<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;


class AbstractOrderHasShipment extends AbstractModel {
	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_order_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_shipment_id;
}