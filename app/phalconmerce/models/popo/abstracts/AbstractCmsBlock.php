<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Db\Column;
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
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_lang_id;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $slug;

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

	/**
	 * @param string $slug
	 * @param int $langId
	 * @param bool $isActive
	 * @return AbstractCmsBlock|bool
	 */
	public static function getBySlugAndLang($slug, $langId, $isActive=true) {
		/** @var \Phalcon\Mvc\Model\Resultset $resultList */
		$resultList = self::find(array(
			'slug = :slug: AND fk_lang_id = :fk_lang_id:'.($isActive ? ' AND status = 1' : ''),
			'bind' => array(
				'slug' => $slug,
				'fk_lang_id' => $langId,
			),
			'bindTypes' => array(
				Column::BIND_PARAM_STR,
				Column::BIND_PARAM_INT
			)
		));
		if (!empty($resultList) && $resultList->count() > 0) {
			return $resultList->getFirst();
		}
		return false;
	}
}