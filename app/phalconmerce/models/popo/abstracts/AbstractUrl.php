<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\AbstractModel;
use Phalconmerce\Models\FkSelect;
use Phalconmerce\Models\Popo\Generators\Popo\PhpClass;
use Phalconmerce\Models\Utils;

abstract class AbstractUrl extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="integer", nullable=false, editable=false)
	 * @var int
	 */
	public $fk_lang_id;

	/**
	 * @Column(type="string", length=32, nullable=true, editable=false)
	 * @Index
	 * @var string
	 */
	public $entity;

	/**
	 * @Column(type="integer", nullable=false, editable=false)
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

	/**
	 * Static method returning fkSelect object used to generated <select> tag in form where category is a foreign key
	 * @return FkSelect
	 */
	public static function fkSelect() {
		// change properties list here
		$displayedProperties = array(
			'fk_lang_id',
			'entity',
			'entityId',
			//'entityName', be careful, very long
			'metaTitle'
		);
		return new FkSelect('id', '[%s] %s#%s - %s', 'Phalconmerce\Models\Popo\\Url', $displayedProperties);
	}

	/**
	 * @param string $entity
	 * @param int $entityId
	 * @param int $langId
	 * @return string
	 */
	public static function getEntityPermalink($entity, $entityId, $langId) {
		$object = self::findFirst(array(
			'entity = :entity: AND entityId = :entity_id: AND fk_lang_id = :fk_lang_id:',
			'bind' => array(
				'entity' => $entity,
				'entity_id' => $entityId,
				'fk_lang_id' => $langId
			)
		));
		if (!empty($object)) {
			return Di::getDefault()->get('url')->getBaseUri().$object->permalink;
		}
		return false;
	}

	/**
	 * @param int $langId
	 * @return bool|static
	 */
	public function getUrlForOtherLang($langId) {
		$object = self::findFirst(array(
			'entity = :entity: AND entityId = :entity_id: AND fk_lang_id = :fk_lang_id:',
			'bind' => array(
				'entity' => $this->entity,
				'entity_id' => $this->entityId,
				'fk_lang_id' => $langId
			)
		));
		if (!empty($object)) {
			return $object;
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getFullUrl() {
		return Di::getDefault()->get('url')->getBaseUri().$this->permalink;
	}

	/**
	 * @return mixed
	 */
	public function getEntityObject() {
		$fqcn = PhpClass::POPO_NAMESPACE.'\\'.Utils::getClassNameFromTableName($this->entity);
		return $fqcn::findFirstById($this->entityId);
	}
}