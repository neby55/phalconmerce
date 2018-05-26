<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Services\Abstracts;

use Phalcon\Db\Column;
use Phalcon\Di;
use Phalconmerce\Models\Popo\Address;
use Phalconmerce\Models\Popo\Customer;
use Phalconmerce\Models\Exceptions\LoginException;

abstract class LoginService extends MainService {
	/** @var  \Phalconmerce\Models\Popo\Abstracts\AbstractCustomer */
	protected $customer;

	// Method called inside the CheckoutController
	public function initLogin() {
		// Retrieve the customer in SESSION and create a new one
		if (!$this->loadCustomer()) {
			$this->customer = new Customer();
		}
	}

	/**
	 * @return bool
	 */
	public function loadCustomer() {
		if (Di::getDefault()->get('session')->has('customer')) {
			$customerTmp = Di::getDefault()->get('session')->get('customer');
			if (is_a($customerTmp, '\Phalconmerce\Models\Popo\Abstracts\AbstractCustomer')) {
				$this->customer = $customerTmp;
				return true;
			}
		}
		return false;
	}

	/**
	 * @return mixed
	 */
	public function saveCustomer() {
		return Di::getDefault()->get('session')->set('customer', $this->customer);
	}

	/**
	 * @return mixed
	 */
	public function disconnectCustomer() {
		return Di::getDefault()->get('session')->remove('customer');
	}

	/**
	 * @param string $email
	 * @return bool
	 */
	public function customerEmailExist($email) {
		$results = Customer::findFirst([
			'email = :email: AND hashedPassword IS NOT NULL AND hashedPassword != ""',
			'bind' => [
				'email' => $email
			]
		]);
		return (!empty($results) && $results->count() > 0);
	}

	/**
	 * @param string $email
	 * @param string $password
	 * @return bool
	 * @throws LoginException
	 */
	public function signIn($email, $password) {
		// Get the customer by email
		/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCustomer $customerObject */
		$customerObject = Customer::findFirstByEmail($email);
		if (!empty($customerObject)) {
			if (password_verify($password, $customerObject->hashedPassword)) {
				$this->customer = $customerObject;
				$this->saveCustomer();
				return true;
			}
			else {
				throw new LoginException('Password is not correct');
			}
		}
		else {
			throw new LoginException('This email address is unknown');
		}
	}

	/**
	 * @param int $customerId
	 * @return bool
	 * @throws LoginException
	 */
	public function signUp($customerId) {
		/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCustomer $customerObject */
		$customerObject = Customer::findById($customerId);
		if (!empty($customerObject)) {
			$this->customer = $customerObject;
			$this->saveCustomer();
			return true;
		}
		else {
			throw new LoginException('This customer does not exist');
		}
	}

	/**
	 * @return bool
	 */
	public function isCustomerConnected() {
		return !empty($this->customer) && is_object($this->customer) && $this->customer->id > 0;
	}

	/**
	 * @return array
	 */
	public function getAddresses() {
		if ($this->isCustomerConnected()) {
			/** @var \Phalcon\Mvc\Model\Resultset $addressList */
			$addressList = Address::find(array(
				'status = 1 AND fk_customer_id = :fk_customer_id',
				'bind' => array(
					'fk_customer_id' => $this->customer->id
				),
				'bindTypes' => array(
					'fk_customer_id' => Column::BIND_PARAM_INT
				)
			));
			if (!empty($addressList) && $addressList->count() > 0) {
				return $addressList->toArray();
			}
		}
	}

	/**
	 * @return \Phalconmerce\Models\Popo\Abstracts\AbstractCustomer
	 */
	public function getCustomer() {
		return $this->customer;
	}

	/**
	 * @param \Phalconmerce\Models\Popo\Abstracts\AbstractCustomer $customer
	 * @return bool
	 */
	public function setCustomer($customer) {
		if (is_object($customer) && is_a($customer, '\Phalconmerce\Models\Popo\Abstracts\AbstractCustomer')) {
			$this->customer = $customer;
			return true;
		}
		return false;
	}
}