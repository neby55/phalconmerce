<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

abstract class AbstractCmsBlock extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_cmscontentbanner_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_cmscontenthtml_id;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="string", length=16, nullable=false)
	 * @var string
	 */
	public $code;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}