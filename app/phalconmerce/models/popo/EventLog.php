<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 03/02/2017
 * Time: 20:05
 */

namespace Phalconmerce\Models\Popo;

use Phalconmerce\Models\Model;

class EventLog extends Model {
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
	public $fk_backendusers_id;

	/**
	 * @Column(type="integer", length=1, nullable=false)
	 * @var int
	 */
	public $type;

	/**
	 * @Column(type="string", length=32, nullable=true)
	 * @var string
	 */
	public $entity;

	/**
	 * @Column(type="string", length=64, nullable=false)
	 * @var string
	 */
	public $message;

	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var int
	 */
	public $date;
}