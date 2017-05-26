<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

abstract class AbstractImage extends AbstractModel {

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
	public $fk_product_id;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $localFile;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $externalUrl;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $cloudinaryPublicId;

	/**
	 * @Column(type="integer", length=4, nullable=false, default=99)
	 * @var int
	 */
	public $position;

}