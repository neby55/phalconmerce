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

abstract class AbstractCmsContentBanner extends AbstractModel {

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
	public $fk_url_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_lang_id;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_image_id;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $externalUrl;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

}