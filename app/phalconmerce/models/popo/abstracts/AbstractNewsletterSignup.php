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

abstract class AbstractNewsletterSignup extends AbstractModel {

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
	 * @Column(type="string", length=255, nullable=false)
	 * @var string
	 */
	public $email;

	/**
	 * @Column(type="boolean", nullable=false, default=0)
	 * @var boolean
	 */
	public $isOptin;

	/**
	 * @Column(type="string", length=32, nullable=true)
	 * @var string
	 */
	public $token;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	public $signupDate;

	/**
	 * @Column(type="string", length=40, nullable=true)
	 * @var string
	 */
	public $ipAddress;

	/**
	 * @Column(type="timestamp", nullable=true)
	 * @var string
	 */
	public $stopDate;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}