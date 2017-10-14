<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModelManyToMany;

abstract class AbstractGroupedProductHasProduct extends AbstractModelManyToMany {

	/**
	 * @Primary
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_grouped_product_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_product_id;

	/**
	 * @Column(type="integer", length=4, nullable=true, default=99)
	 * @var int
	 */
	public $position;

	/**
	 * @return mixed
	 */
	public static function getGroupedClassName() {
		return str_replace('HasProduct', '', __CLASS__);
	}
}