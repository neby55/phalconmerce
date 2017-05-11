<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

class AbstractCurrency extends AbstractModel {

	/**
	 * @Primary
	 * @Column(type="string", length=3, nullable=false)
	 * @var string
	 */
	public $id;

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