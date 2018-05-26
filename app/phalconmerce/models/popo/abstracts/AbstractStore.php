<?php

namespace Phalconmerce\Models\Popo\Abstracts;

use Phalconmerce\Models\AbstractModel;

// Remove @Api annotations to disable acces to this class from API service
/**
 * @Api
 */
class AbstractStore extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $id;

	/**
	 * @Column(type="string", length=64, nullable=true)
	 * @var string
	 */
	public $name;

	/**
	 * @Column(type="string", length=60, nullable=false)
	 * @var string
	 */
	public $firstLine;

	/**
	 * @Column(type="string", length=60, nullable=true)
	 * @var string
	 */
	public $secondLine;

	/**
	 * @Column(type="string", length=60, nullable=true)
	 * @var string
	 */
	public $thirdLine;

	/**
	 * @Column(type="string", length=20, nullable=false)
	 * @var string
	 */
	public $zipCode;

	/**
	 * @Column(type="string", length=60, nullable=false)
	 * @var string
	 */
	public $city;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	public $fk_country_id;

	/**
	 * @Column(type="string", length=20, nullable=true)
	 * @var string
	 */
	public $phoneNumber;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 * @var string
	 */
	public $email;

	/**
	 * @Column(type="string", length=128, nullable=true)
	 * @var string
	 */
	public $hours;

	/**
	 * @Column(type="gps", nullable=true)
	 * @var float
	 */
	public $lat;

	/**
	 * @Column(type="gps", nullable=true)
	 * @var float
	 */
	public $long;

	/**
	 * @Column(type="integer", length=4, nullable=true, default=999)
	 * @Index
	 * @var int
	 */
	public $position;

	/**
	 * @Column(type="integer", length=2, nullable=false, default=0)
	 * @var int
	 */
	public $status;
}
