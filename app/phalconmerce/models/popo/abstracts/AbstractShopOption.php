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

	const NAME_IS_WEBSITE_ACTIVE = 'shop-is_website_active';
	const NAME_IS_SHOP_ACTIVE = 'shop-is_shop_active';
	const NAME_IS_PO_INDEX_ACTIVE = 'shop-is_po_index_active';
	const NAME_ENABLE_WEBSITE_BY_IP = 'shop-enable_website_by_ip';
	const NAME_ENABLE_SHOP_BY_IP = 'shop-enable_shop_by_ip';
	const NAME_FREE_SHIPPING_AMOUNT = 'shop-free_shipping_amount_vat_included';

	/**
	 * @return array
	 */
	public static function getNames() {
		return array(
			// Shop
			static::NAME_IS_WEBSITE_ACTIVE => '1 to set website active',
			static::NAME_IS_SHOP_ACTIVE => '1 to set shop active',
			'shop-title' => 'Shop\'s title',
			'shop-default_currency' => 'Default currency',
			'shop-default_lang' => 'Default language',
			static::NAME_ENABLE_WEBSITE_BY_IP => 'Define list of IP adresses authorized to see website if it\'s disabled',
			static::NAME_ENABLE_SHOP_BY_IP => 'Define list of IP adresses authorized to see shop if it\'s disabled',
			static::NAME_IS_PO_INDEX_ACTIVE => '1 to enable translation indexation',
			static::NAME_FREE_SHIPPING_AMOUNT => 'Minimum amount to have shipping free',
			// Mailer
			'mailer-sender' => 'Shop emails sender',
			'mailer-host' => 'SMTP host',
			'mailer-port' => 'SMTP port',
			'mailer-username' => 'SMTP username',
			'mailer-password' => 'SMTP password',
			'mailer-smtp_secure' => 'SMTP security (tls/ssl/empty)',
			'mailer-charset' => 'SMTP charset',
		);
	}

	/**
	 * Static method returning possibles datas in <select> tag for the field "example"
	 * @return array
	 */
	public static function nameSelectOptions() {
		return array_merge(
			array(
				0 => 'choose'
			),
			static::getNames()
		);
	}
}