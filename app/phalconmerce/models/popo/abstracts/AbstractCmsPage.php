<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\AbstractDesignedModel;
use Phalconmerce\Models\Popo\Url;

abstract class AbstractCmsPage extends AbstractDesignedModel {

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
	 * @Column(type="string", length=16, nullable=true)
	 * @var string
	 */
	public $location;

	/**
	 * @Column(type="integer", length=4, nullable=true, default=99)
	 * @var int
	 */
	public $position;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/**
	 * @param int $id
	 * @param int $langId
	 * @return mixed
	 */
	public static function getPermalinkFromId($id, $langId) {
		return Url::getEntityPermalink('cms_page', $id, $langId);
	}

	/**
	 * Static method returning possibles datas in <select> tag for the field "location"
	 * @return array
	 */
	public static function locationSelectOptions() {
		// You should overridre this method
		return array(
			'' => 'choose',
			'header' => 'header',
			'footer' => 'footer',
			'sidebar_left' => 'left sidebar',
			'sidebar_right' => 'right sidebard'
		);
	}
}