<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

class AbstractCmsLocation extends AbstractModel {

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
	public $fk_cmsdesign_id;

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
	 * @Column(type="integer", length=4, nullable=false, default=0)
	 * @Index
	 * @var int
	 */
	public $row;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=99)
	 * @Index
	 * @var int
	 */
	public $position;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=3)
	 * @var int
	 */
	public $colMdValue;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=3)
	 * @var int
	 */
	public $colSmValue;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=3)
	 * @var int
	 */
	public $colXsValue;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}