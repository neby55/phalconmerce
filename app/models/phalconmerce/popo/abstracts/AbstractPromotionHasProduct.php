<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

abstract class AbstractPromotionHasProduct extends AbstractModel {

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_promotion_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_product_id;

	/**
	 * @Column(type="integer", length=4, nullable=false, default=999)
	 * @Index
	 * @var int
	 */
	public $position;

	/**
	 * Overriding AbstractModel::initialize() to force the prefix
	 */
	public function initialize() {
		// Set the prefix
		$this->prefix = 'php_';

		parent::initialize();
	}
}