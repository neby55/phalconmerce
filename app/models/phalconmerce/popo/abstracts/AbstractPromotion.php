<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

abstract class AbstractPromotion extends AbstractModel {

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
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $type;

	/**
	 * @Column(type="float", nullable=false)
	 * @var float
	 */
	public $value;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $startDate;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $endDate;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	// TODO find a way to define available promotion types (const no, static maybe, function last option)

	/**
	 * Overriding AbstractModel::initialize() to force the prefix
	 */
	public function initialize() {
		// Set the prefix
		$this->prefix = 'pmt_';

		parent::initialize();
	}

}