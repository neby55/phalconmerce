<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractCurrency extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=3, nullable=false)
	 * @var string
	 */
	public $isoCode;

	/**
	 * @Column(type="string", length=4, nullable=false)
	 * @var string
	 */
	public $sigle;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $exchangeRate;

	/**
	 * @Column(type="boolean", nullable=false)
	 * @var boolean
	 */
	public $isProductsDefault;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}