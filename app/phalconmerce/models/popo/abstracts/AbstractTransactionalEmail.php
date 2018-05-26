<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\AbstractDesignedModel;
use Phalconmerce\Models\Popo\Url;

abstract class AbstractTransactionalEmail extends AbstractDesignedModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=78, nullable=false)
	 * @var string
	 */
	public $subject;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $event;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;

	/**
	 * @var array
	 */
	public static $eventsList = array(
		self::EVENT_NEW_ACCOUNT => 'New account',
		self::EVENT_FORGOT_PASSWORD => 'Forgotten password',
		self::EVENT_NEWSLETTER_SIGNUP => 'Signup to newsletter',
		self::EVENT_NEWSLETTER_EMAIL_CONFIRM => 'Email confirmation for newsletter',
		self::EVENT_ORDER_CONFIRMED => 'Order confirmed',
		self::EVENT_ORDER_CANCELLED => 'Order canceled',
		self::EVENT_FRIEND_SPONSORING => 'Friend sponsoring invitation',
	);

	const EVENT_NEW_ACCOUNT = 1;
	const EVENT_FORGOT_PASSWORD = 2;
	const EVENT_NEWSLETTER_SIGNUP = 3;
	const EVENT_NEWSLETTER_EMAIL_CONFIRM = 4;
	const EVENT_ORDER_CONFIRMED = 5;
	const EVENT_ORDER_CANCELLED = 6;
	const EVENT_FRIEND_SPONSORING = 7;

	/**
	 * Static method returning possibles datas in <select> tag for the field "event"
	 * @return array
	 */
	public static function eventSelectOptions() {
		return array_merge(array(0=>'-'), self::$eventsList);
	}

}