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

	// You can add names in the "child" initialize() method
	public static $names = array(
		self::NAME_IS_WEBSITE_ACTIVE,
		self::NAME_IS_SHOP_ACTIVE,
		'shopTitle',
		'shopDefaultCurrency',
		'shopDefaultLang',
		self::NAME_ENABLE_WEBSITE_BY_IP,
		self::NAME_ENABLE_SHOP_BY_IP,
		self::NAME_IS_PO_INDEX_ACTIVE
	);
}