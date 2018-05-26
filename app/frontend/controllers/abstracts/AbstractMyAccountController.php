<?php

namespace Frontend\Controllers\Abstracts;

use Frontend\Controllers\ControllerBase;

abstract class AbstractMyaccountController extends ControllerBase {

	public function initialize() {
		parent::initialize();

		$this->myAccount->initMyAccount();
	}

	/**
	 * @return bool
	 */
	protected function checkCustomerConnection() {
		// If not connected
		if (!$this->myAccount->isCustomerConnected()) {
			return $this->redirectToRoute($this->myAccount->getSigninRouteName());
		}
		return true;
	}

	public function indexAction() {
		return $this->checkCustomerConnection();
	}

	public function signinAction() {
		// If already connected
		if ($this->myAccount->isCustomerConnected()) {
			return $this->redirectToRoute($this->myAccount->getIndexRouteName());
		}
	}

	public function forgotPasswordAction() {

	}

	public function resetPasswordAction() {

	}

	public function signoutAction() {
		if ($this->myAccount->isCustomerConnected()) {
			// Disconnect user on session
			$this->myAccount->disconnectCustomer();
			// then redirect to signin page
			return $this->redirectToRoute($this->myAccount->getSigninRouteName());
		}
		return false;
	}
}

