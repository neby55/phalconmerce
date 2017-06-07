<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;


abstract class AbstractState extends AbstractModel {
	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_country_id;
}