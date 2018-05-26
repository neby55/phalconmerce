<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModelManyToMany;

abstract class AbstractStoreHasCategory extends AbstractModelManyToMany {

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_store_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_category_id;

	/**
	 * @Column(type="integer", length=4, nullable=false, default=999)
	 * @Index
	 * @var int
	 */
	public $position;
}