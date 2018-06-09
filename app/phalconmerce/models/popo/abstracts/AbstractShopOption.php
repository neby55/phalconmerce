<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

class AbstractShopOption extends AbstractModel {
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
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $value;

	const NAME_IS_WEBSITE_ACTIVE = 'isWebsiteActive';
	const NAME_IS_SHOP_ACTIVE = 'isShopActive';
	const NAME_IS_PO_INDEX_ACTIVE = 'isPoIndexActive';
	const NAME_ENABLE_WEBSITE_BY_IP = 'enableWebsiteByIP';
	const NAME_ENABLE_SHOP_BY_IP = 'enableShopByIP';

	/**
	 * @return array
	 */
	public static function getNames() {
		return array(
			static::NAME_IS_WEBSITE_ACTIVE,
			static::NAME_IS_SHOP_ACTIVE,
			'shopTitle',
			'shopDefaultCurrency',
			'shopDefaultLang',
			static::NAME_ENABLE_WEBSITE_BY_IP,
			static::NAME_ENABLE_SHOP_BY_IP,
			static::NAME_IS_PO_INDEX_ACTIVE
		);
	}
}