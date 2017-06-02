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

abstract class AbstractManufacturer extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=128, nullable=false)
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @Translate
	 * @var string
	 */
	public $shortDescription;

	/**
	 * @Column(type="text", nullable=true)
	 * @Translate
	 * @var string
	 */
	public $description;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $imageLocalFilename;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}