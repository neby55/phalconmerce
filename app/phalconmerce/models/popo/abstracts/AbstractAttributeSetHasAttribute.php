<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModelManyToMany;

abstract class AbstractAttributeSetHasAttribute extends AbstractModelManyToMany {

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_attributeset_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_attribute_id;

	/**
	 * @Column(type="integer", length=4, nullable=false, default=999)
	 * @Index
	 * @var int
	 */
	public $position;
}