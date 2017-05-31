<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;

abstract class AbstractOrderReturn extends AbstractModel {

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
	public $fk_order_id;

	/**
	 * @Column(type="timestamp", nullable=true)
	 * @var string
	 */
	public $demandDate;

	/**
	 * @Column(type="timestamp", nullable=true)
	 * @var string
	 */
	public $acceptedDate;

	/**
	 * @Column(type="timestamp", nullable=true)
	 * @var string
	 */
	public $receiptDate;

	/**
	 * @Column(type="text", nullable=false)
	 * @var string
	 */
	public $customerMessage;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

}