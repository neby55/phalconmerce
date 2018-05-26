<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModelManyToMany;

abstract class AbstractCategoryHasFilter extends AbstractModelManyToMany {

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_category_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_filter_id;

	/**
	 * @Column(type="integer", length=4, nullable=true, default=999)
	 * @Index
	 * @var int
	 */
	public $position;
}