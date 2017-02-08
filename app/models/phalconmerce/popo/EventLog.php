<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 03/02/2017
 * Time: 20:05
 */

namespace Phalconmerce\Popo;

use Phalconmerce\Model;

class EventLog extends Model {
	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	protected $id;
	/**
	 * @Column(type="integer", length=1, nullable=false)
	 * @var int
	 */
	protected $type;
	/**
	 * @Column(type="string", length=16, nullable=true)
	 * @var string
	 */
	protected $entity;
	/**
	 * @Column(type="string", length=32, nullable=false)
	 * @var string
	 */
	protected $message;
	/**
	 * @Column(type="timestamp", nullable=false)
	 * @var string
	 */
	protected $date;

	public function initialze() {
		parent::initialize();
	}
}