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
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $name;

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
}