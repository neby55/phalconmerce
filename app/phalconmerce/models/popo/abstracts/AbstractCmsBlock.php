<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

abstract class AbstractCmsBlock extends AbstractModel {

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
	 * @Column(type="string", length=16, nullable=false)
	 * @var string
	 */
	public $code;

	/**
	 * @Column(type="text", nullable=true)
	 * @var string
	 */
	public $html;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	public static function getBySlug($slug, $isActive=true) {
		self::findFirst(array(
			'code = :code:'.($isActive ? ' AND '),
		))
	}
}