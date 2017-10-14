<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\AbstractModel;
use Phalconmerce\Models\Utils;
use Phalconmerce\Models\Popo\Seo;

abstract class AbstractSeo extends AbstractModel {

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
	 * @Index
	 * @var string
	 */
	public $routeName;

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
	 * Static method returning possibles datas in <select> tag for the field "example"
	 * @return array
	 */
	public static function routeNameSelectOptions() {
		// You should override this method
		return array(
			'' => 'choose',
			'default' => 'default',
			'home' => 'home',
		);
	}

	/**
	 * @param string $routeName
	 * @param int $langId
	 * @return AbstractSeo|bool
	 */
	public static function getCacheByRouteName($routeName, $langId) {
		$data = Utils::loadData('seos');
		if (!empty($data)) {
			if (!empty($data[$routeName])) {
				if (!empty($data[$routeName][$langId])) {
					return $data[$routeName][$langId];
				}
			}
		}
		return false;
	}

	public static function updateSeoCache() {
		$allSeo = Seo::find('status = 1');

		$data = array();

		/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractSeo $currentSeobject */
		foreach ($allSeo as $currentSeobject) {
			if (!empty($currentSeobject->routeName)) {
				$data[$currentSeobject->routeName][$currentSeobject->fk_lang_id] = $currentSeobject;
			}
		}

		return Utils::saveData($data, 'seos');
	}
}