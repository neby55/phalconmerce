<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

abstract class AbstractGroupedProductHasSimpleProduct extends AbstractModel {

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_groupedproduct_id;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_simpleproduct_id;
}