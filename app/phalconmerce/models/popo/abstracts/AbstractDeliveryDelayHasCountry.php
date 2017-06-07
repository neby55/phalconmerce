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

abstract class AbstractDeliveryDelayHasCountry extends AbstractModel {

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_deliverydelay_id;

	/**
	 * @Primary
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_country_id;

	/**
	 * Overriding AbstractModel::initialize() to force the prefix
	 */
	public function initialize() {
		// Set the prefix
		$this->prefix = 'ddhc_';

		parent::initialize();
	}
}