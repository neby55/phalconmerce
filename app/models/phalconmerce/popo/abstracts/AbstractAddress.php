<?php

namespace Phalconmerce\Popo\Abstracts;

use Phalconmerce\AbstractModel;


class AbstractAddress extends AbstractModel {

	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	protected $id;

	/**
	 * @Column(type="string", length=32, nullable=true)
	 * @var string
	 */
	protected $lastName;

	/**
	 * @Column(type="string", length=32, nullable=true)
	 * @var string
	 */
	protected $firstName;

	/**
	 * @Column(type="string", length=32, nullable=true)
	 * @var string
	 */
	protected $company;

	/**
	 * @Column(type="string", length=60, nullable=false)
	 * @var string
	 */
	protected $firstLine;

	/**
	 * @Column(type="string", length=60, nullable=true)
	 * @var string
	 */
	protected $secondLine;

	/**
	 * @Column(type="string", length=60, nullable=true)
	 * @var string
	 */
	protected $thirdLine;

	/**
	 * @Column(type="string", length=20, nullable=false)
	 * @var string
	 */
	protected $zipCode;

	/**
	 * @Column(type="string", length=60, nullable=false)
	 * @var string
	 */
	protected $city;

	/**
	 * @Column(type="integer", nullable=false, default=0)
	 * @var int
	 */
	protected $status;

	/**
	 * @Column(type="integer", nullable=false)
	 * @var int
	 */
	protected $fk_country_id;

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * @param string $lastName
	 */
	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	/**
	 * @return string
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * @param string $firstName
	 */
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	/**
	 * @return string
	 */
	public function getCompany() {
		return $this->company;
	}

	/**
	 * @param string $company
	 */
	public function setCompany($company) {
		$this->company = $company;
	}

	/**
	 * @return string
	 */
	public function getFirstLine() {
		return $this->firstLine;
	}

	/**
	 * @param string $firstLine
	 */
	public function setFirstLine($firstLine) {
		$this->firstLine = $firstLine;
	}

	/**
	 * @return string
	 */
	public function getSecondLine() {
		return $this->secondLine;
	}

	/**
	 * @param string $secondLine
	 */
	public function setSecondLine($secondLine) {
		$this->secondLine = $secondLine;
	}

	/**
	 * @return string
	 */
	public function getThirdLine() {
		return $this->thirdLine;
	}

	/**
	 * @param string $thirdLine
	 */
	public function setThirdLine($thirdLine) {
		$this->thirdLine = $thirdLine;
	}

	/**
	 * @return string
	 */
	public function getZipCode() {
		return $this->zipCode;
	}

	/**
	 * @param string $zipCode
	 */
	public function setZipCode($zipCode) {
		$this->zipCode = $zipCode;
	}

	/**
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * @param string $city
	 */
	public function setCity($city) {
		$this->city = $city;
	}

	/**
	 * @return int
	 */
	public function getFkCountryId() {
		return $this->fk_countryId;
	}

	/**
	 * @param int $fk_countryId
	 */
	public function setFkCountryId($fk_countryId) {
		$this->fk_countryId = $fk_countryId;
	}


}