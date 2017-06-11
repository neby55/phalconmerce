<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModelManyToMany;

abstract class AbstractGroupedProductHasProduct extends AbstractModelManyToMany {

	/**
	 * @Primary
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_groupedproduct_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_product_id;
}