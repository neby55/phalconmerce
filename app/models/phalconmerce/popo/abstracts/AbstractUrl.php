<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

class AbstractUrl extends AbstractModel {

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
	public $fk_lang_id;

	/**
	 * @Column(type="string", length=32, nullable=true)
	 * @Index
	 * @var string
	 */
	public $entity;

	/**
	 * @Column(type="integer", nullable=false)
	 * @Index
	 * @var int
	 */
	public $entityId;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @Index
	 * @var string
	 */
	public $permalink;

	/**
	 * @Column(type="string", length=128, nullable=true)
	 * @var string
	 */
	public $metaTitle;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $metaDescription;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $metaKeywords;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

}