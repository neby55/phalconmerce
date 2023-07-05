<?php

namespace Frontend\Controllers\Abstracts;

use Frontend\Controllers\ControllerBase;
use Phalconmerce\Models\Utils;

abstract class AbstractCheckoutController extends ControllerBase {

	public function initialize() {
		parent::initialize();

		$this->checkout->initCheckout();
	}

	public function indexAction() {
		// indexAction is like a dispatcher

		// If not connected
		if ($this->checkout->isCustomerConnected()) {
			$this->checkout->updateOrder();
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

	public function orderConfirmedAction() {
		$order = $this->checkout->getOrder();
		// If no Order in session, back to home
		if (empty($order) || !is_object($order) || $order->id <= 0) {
			return $this->redirectToRoute('home');
		}
		// Adding order to the view
		$this->view->setVar('currentOrder', $order);

		// Removing order and cart on session
		$this->checkout->removeOrder();
		$this->frontendService->removeCart();
		$this->frontendService->deleteVoucher();
	}

	public function orderCancelledAction() {
		$order = $this->checkout->getOrder();
		// If no Order in session, back to home
		if (empty($order) || !is_object($order) || $order->id <= 0) {
			return $this->redirectToRoute('home');
		}
		// Adding order to the view
		$this->view->setVar('currentOrder', $this->checkout->getOrder());
	}
}

