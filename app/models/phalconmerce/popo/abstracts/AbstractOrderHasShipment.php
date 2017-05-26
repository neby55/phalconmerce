<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;


abstract class AbstractOrderHasShipment extends AbstractModel {
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

	/**
	 * Overriding AbstractModel::initialize() to force the prefix
	 */
	public function initialize() {
		// Set the prefix
		$this->prefix = 'ohs_';

		parent::initialize();
	}
}