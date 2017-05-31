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

abstract class AbstractInvoice extends AbstractModel {

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
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $number;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $pdfFile;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

}