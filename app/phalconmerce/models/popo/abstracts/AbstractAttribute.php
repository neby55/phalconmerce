<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\AbstractModel;

abstract class AbstractAttribute extends AbstractModel {

	const TYPE_INT = 1;
	const TYPE_FLOAT = 2;
	const TYPE_STRING = 3;
	const TYPE_DROPDOWN = 4;
	const TYPE_BOOLEAN = 5;

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
	 * @Column(type="string", length=128, nullable=true)
	 * @var string
	 */
	public $helpText;

	/**
	 * @Column(type="string", length=64, nullable=false)
	 * @Translate
	 * @var string
	 */
	public $label;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $type;

	/**
	 * @Column(type="boolean", nullable=false, default=1)
	 * @var int
	 */
	public $isConfigurable;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $cmsBlockSlug;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/**
	 * @return bool
	 */
	public function isConfigurable() {
		return $this->isConfigurable == 1;
	}

	public function hasCmsBlock() {
		return !empty($this->cmsBlockSlug);
	}
}