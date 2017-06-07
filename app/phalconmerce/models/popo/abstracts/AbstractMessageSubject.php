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

abstract class AbstractMessageSubject extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="integer", nullable=true)
	 * @var int
	 */
	public $fk_messagecategory_id;

	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	public $subject;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

}