<?php

namespace Frontend\Controllers\Abstracts;

use Frontend\Controllers\ControllerBase;

abstract class AbstractCheckoutController extends ControllerBase {

	public function initialize() {
		parent::initialize();

		$this->checkout->initCheckout();
	}

	public function indexAction() {
		// indexAction is like a dispatcher

		// If not connected
		if ($this->checkout->isCustomerConnected()) {
			return $this->redirectToRoute($this->checkout->getOnepageRouteName());
		}
		else {
			return $this->redirectToRoute($this->checkout->getSigninRouteName());
		}
	}

	public function onepageAction() {
		// If not connected
		if (!$this->checkout->isCustomerConnected()) {
			return $this->redirectToRoute($this->checkout->getSigninRouteName());
		}
	}

	public function signinAction() {

	}

	public function signupAction() {

	}

	public function forgotPasswordAction() {

	}

	public function resetPasswordAction() {

	}

	public function paymentAction() {
		// If not connected
		if (!$this->checkout->isCustomerConnected()) {
			return $this->redirectToRoute($this->checkout->getSigninRouteName());
		}
	}
}

