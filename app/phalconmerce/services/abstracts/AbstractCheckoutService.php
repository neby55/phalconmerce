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
use Phalconmerce\Models\Generic\Route as CheckoutRoute;
use Phalconmerce\Models\Popo\PaymentMethod;
use Phalconmerce\Models\Popo\DeliveryDelayHasCountry;
use Phalconmerce\Models\Popo\DeliveryDelay;
use Phalconmerce\Models\Popo\Expeditor;
use Phalconmerce\Models\Popo\Address;
use Phalconmerce\Models\Popo\Order;
use Phalconmerce\Models\Popo\Customer;
use Phalconmerce\Models\Exceptions\CheckoutException;
use Phalconmerce\Models\Utils;

abstract class AbstractCheckoutService extends LoginService {
	/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractOrder */
	protected $order;

	// Constants defining checkout routes' name (can be overridden)
	const CHECKOUT_URL_FOLDER = '/checkout';
	const CHECKOUT_SIGNIN_ROUTE_NAME = 'checkout_signin';
	const CHECKOUT_SIGNUP_ROUTE_NAME = 'checkout_signup';
	const CHECKOUT_FORGOTPASSWORD_ROUTE_NAME = 'checkout_forgotpassword';
	const CHECKOUT_RESETPASSWORD_ROUTE_NAME = 'checkout_resetpassword';
	const CHECKOUT_ONEPAGE_ROUTE_NAME = 'checkout_onepage';
	const CHECKOUT_PAYMENT_ROUTE_NAME = 'checkout_payment';

	public function __construct() {

	}

	// Method called inside the CheckoutController
	public function initCheckout() {
		// Retrieve the customer in SESSION and create a new one
		$this->initLogin();
		// Retrieve the order in SESSION and create a new one
		if (!$this->loadOrder()) {
			$this->order = new Order();
		}
	}

	/**
	 * @return \Phalconmerce\Models\Generic\Abstracts\AbstractRoute[]
	 */
	public static function getRoutes() {
		$routesList = array();

		// Signin
		$routesList[] = new CheckoutRoute(
			static::CHECKOUT_URL_FOLDER.'/signin',
			'Checkout',
			'signin',
			static::CHECKOUT_SIGNIN_ROUTE_NAME
		);
		// Signup
		$routesList[] = new CheckoutRoute(
			static::CHECKOUT_URL_FOLDER.'/signup',
			'Checkout',
			'signup',
			static::CHECKOUT_SIGNUP_ROUTE_NAME
		);
		// ForgotPassword
		$routesList[] = new CheckoutRoute(
			static::CHECKOUT_URL_FOLDER.'/forgot-password',
			'Checkout',
			'forgotPassword',
			static::CHECKOUT_FORGOTPASSWORD_ROUTE_NAME
		);
		// ResetPassword
		$routesList[] = new CheckoutRoute(
			static::CHECKOUT_URL_FOLDER.'/reset-password',
			'Checkout',
			'resetPassword',
			static::CHECKOUT_RESETPASSWORD_ROUTE_NAME
		);
		// Onepage
		$routesList[] = new CheckoutRoute(
			static::CHECKOUT_URL_FOLDER.'/onepage',
			'Checkout',
			'onepage',
			static::CHECKOUT_ONEPAGE_ROUTE_NAME
		);
		// Payment Form Page
		$routesList[] = new CheckoutRoute(
			static::CHECKOUT_URL_FOLDER.'/payment',
			'Checkout',
			'payment',
			static::CHECKOUT_PAYMENT_ROUTE_NAME
		);

		return $routesList;
	}

	/**
	 * @return string
	 */
	public static function getSigninRouteName() {
		return static::CHECKOUT_SIGNIN_ROUTE_NAME;
	}

	/**
	 * @return string
	 */
	public static function getSignupRouteName() {
		return static::CHECKOUT_SIGNUP_ROUTE_NAME;
	}

	/**
	 * @return string
	 */
	public static function getForgotPasswordRouteName() {
		return static::CHECKOUT_FORGOTPASSWORD_ROUTE_NAME;
	}

	/**
	 * @return string
	 */
	public static function getResetPasswordRouteName() {
		return static::CHECKOUT_RESETPASSWORD_ROUTE_NAME;
	}

	/**
	 * @return string
	 */
	public static function getOnepageRouteName() {
		return static::CHECKOUT_ONEPAGE_ROUTE_NAME;
	}

	/**
	 * @return string
	 */
	public static function getPaymentRouteName() {
		return static::CHECKOUT_PAYMENT_ROUTE_NAME;
	}

	/**
	 * @param string $email
	 * @param string $password
	 * @return bool
	 * @throws \Phalconmerce\Models\Exceptions\LoginException
	 */
	public function signIn($email, $password) {
		if (parent::signIn($email, $password)) {
			$this->newOrder($this->customer->id);
			return true;
		}
		return false;
	}

	/**
	 * @param int $customerId
	 * @return bool
	 * @throws \Phalconmerce\Models\Exceptions\LoginException
	 */
	public function signUp($customerId) {
		if (parent::signUp($customerId)) {
			$this->newOrder($this->customer->id);
			return true;
		}
		return false;
	}


	/**
	 * @param int $customerId
	 * @return bool
	 */
	public function newOrder($customerId=0) {
		// Services
		/** @var \Phalconmerce\Services\Abstracts\AbstractTranslationService $translationService */
		$translationService = Di::getDefault()->get('translation');
		/** @var \Phalconmerce\Services\Abstracts\AbstractFrontendService $frontendService */
		$frontendService = Di::getDefault()->get('frontendService');

		// If already added to DB
		if ($this->order->id > 0) {
			// Then, save each cart content to DB and Session
			$frontendService->saveCartToDb($this->order->id);

			// save order in session
			$this->saveOrder();

			return true;
		}
		else {
			// First get Cart Content
			$cartLines = $frontendService->getCart();
			// If there is a cart
			if (!empty($cartLines)) {
				// Create the order
				/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractOrder $orderObject */
				$this->order = new Order();
				$this->order->fk_currency_id = $translationService->getCurrency()->id;
				$this->order->fk_lang_id = $translationService->getLangId();
				$this->order->fk_customer_id = $customerId;
				$this->order->fk_payment_method_id = 0;
				$this->order->fk_address_id = 0;
				$this->order->fk_delivery_delay_id = 0;
				$this->order->fk_voucher_id = 0; // TODO
				$this->order->amountDiscount = 0; // TODO
				$this->order->amountVatIncluded = $frontendService->getCartSubTotalVatIncluded(); // TODO check Totals
				$this->order->amountVatExcluded = $frontendService->getCartSubTotalVatExcluded();
				$this->order->isGift = false;
				$this->order->giftMessage = '';
				$this->order->status = Order::STATUS_NONE;

				// save the order on DB
				if ($this->order->save()) {
					// Then, save each cart content to DB and Session
					$frontendService->saveCartToDb($this->order->id);

					// save order in session
					$this->saveOrder();

					return true;
				}
			}
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function loadOrder() {
		if (Di::getDefault()->get('session')->has('order')) {
			$orderTmp = Di::getDefault()->get('session')->get('order');
			if (is_a($orderTmp, '\Phalconmerce\Models\Popo\Abstracts\AbstractOrder')) {
				$this->order = $orderTmp;
				return true;
			}
		}
		return false;
	}

	/**
	 * @return mixed
	 */
	public function saveOrder() {
		return Di::getDefault()->get('session')->set('order', $this->order);
	}

	/**
	 * @return bool|\Phalconmerce\Models\Popo\Abstracts\AbstractAddress
	 */
	public function getBillingAddress() {
		// If shippingAddress is set
		if ($this->isCustomerConnected()) {
			if ($this->customer->fk_address_id > 0) {
				/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractAddress $shippingAddress */
				$billingAddress = $this->customer->getAddress();
				if (!empty($billingAddress)) {
					return $billingAddress;
				}
			}
		}
		return false;
	}

	/**
	 * @param int $id
	 * @return bool
	 * @throws CheckoutException
	 */
	public function setBillingAddress($id) {
		$address = Address::findById($id);
		if (!empty($address)) {
			if (is_a($address, '\Phalconmerce\Models\Popo\Abstracts\AbstractAddress')) {
				// If this address is active
				if ($address->status == 1) {
					$this->customer->fk_address_id = $id;
					// Save to DB
					$this->customer->save();
					// Save to session
					$this->saveCustomer();

					return true;
				}
				else {
					throw new CheckoutException('Given billing address is disabled');
				}
			}
			else {
				throw new CheckoutException('Found billing address is invalid');
			}
		}
		else {
			throw new CheckoutException('This billing addess does not exists');
		}
	}

	/**
	 * @return bool|\Phalconmerce\Models\Popo\Abstracts\AbstractAddress
	 */
	public function getShippingAddress() {
		// If shippingAddress is set
		if ($this->order->fk_address_id > 0) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractAddress $shippingAddress */
			$shippingAddress = $this->order->getAddress();
			if (!empty($shippingAddress)) {
				return $shippingAddress;
			}
		}
		return false;
	}

	/**
	 * @param int $id
	 * @return bool
	 * @throws CheckoutException
	 */
	public function setShippingAddress($id) {
		$address = Address::findById($id);
		if (!empty($address)) {
			if (is_a($address, '\Phalconmerce\Models\Popo\Abstracts\AbstractAddress')) {
				// If this address is active
				if ($address->status == 1) {
					$this->order->fk_address_id = $id;
					// Save to DB
					$this->order->save();
					// Save to session
					$this->saveOrder();

					return true;
				}
				else {
					throw new CheckoutException('Given shipping address is disabled');
				}
			}
			else {
				throw new CheckoutException('Found shipping address is invalid');
			}
		}
		else {
			throw new CheckoutException('This shipping addess does not exists');
		}
	}

	/**
	 * @return array|bool
	 */
	public function getShippingMethods() {
		$deliveryDelaysList = $this->getDeliveryDelays();
		if (!empty($deliveryDelaysList)) {
			$results = array();
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractDeliveryDelay $currentDeliveryDelay */
			foreach ($deliveryDelaysList as $currentDeliveryDelay) {
				$currentExpeditor = $currentDeliveryDelay->getExpeditor();
				if (!empty($currentExpeditor)) {
					$results[$currentDeliveryDelay->id] = array(
						'name' => $currentDeliveryDelay->name,
						'description' => $currentDeliveryDelay->description,
						'amount_vat_excluded' => $currentExpeditor->amountVatExcluded,
						'expeditor_name' => $currentExpeditor->name
					);
				}
			}
			return $results;
		}
		return false;
	}

	/**
	 * @param int $id
	 * @return bool
	 * @throws CheckoutException
	 */
	public function setShippingMethod($id) {
		$deliveryDelaysList = $this->getDeliveryDelays();
		if (array_key_exists($id, $deliveryDelaysList)) {
			$currentDeliveryDelay = $deliveryDelaysList[$id];
			// If enabled
			if ($currentDeliveryDelay->status == 1) {
				$this->order->fk_delivery_delay_id = $currentDeliveryDelay->id;
				// Save to DB
				$this->order->save();
				// Save to session
				$this->saveOrder();

				return true;
			}
			else {
				throw new CheckoutException('Given shipping method is disabled');
			}
		}
		else {
			throw new CheckoutException('This shipping method does not exists for this country');
		}
	}

	/**
	 * @return \Phalconmerce\Models\Popo\Abstracts\AbstractPaymentMethod[]
	 */
	public function getPaymentMethods() {
		// Get every payment Method enabled
		$results = PaymentMethod::find(array(
			'status = 1 AND (
				fk_currency_id IS NULL
				OR fk_currency_id = 0
				OR fk_currency_id = :fk_currency_id:
			) AND (
				minimumAmount IS NULL
				OR minimumAmount = 0
				OR :orderAmount: >= minimumAmount
			) AND (
				maximumAmount IS NULL
				OR maximumAmount = 0
				OR :orderAmount: <= maximumAmount
			)',
			'order' => 'position ASC',
			'bind' => array(
				'fk_currency_id' => $this->order->fk_currency_id,
				'orderAmount' => $this->order->amountVatIncluded
			),
			'bindTypes' => array(
				'fk_currency_id' => Column::BIND_PARAM_INT,
				'orderAmount' => Column::BIND_PARAM_DECIMAL
			)
		));
		if (!empty($results) && $results->count() > 0) {
			return $results->toArray();
		}
		return array();
	}

	/**
	 * @param int $id
	 * @return bool
	 * @throws CheckoutException
	 */
	public function setPaymentMethod($id) {
		$paymentMethod = PaymentMethod::findById($id);
		if (!empty($paymentMethod)) {
			if (is_a($paymentMethod, '\Phalconmerce\Models\Popo\Abstracts\AbstractPaymentMethod')) {
				// If this payment method is active
				if ($paymentMethod->status == 1) {
					$this->order->fk_payment_method_id = $id;
					// Save to DB
					$this->order->save();
					// Save to session
					$this->saveOrder();

					return true;
				}
				else {
					throw new CheckoutException('Given payment method is disabled');
				}
			}
			else {
				throw new CheckoutException('Found payment method is invalid');
			}
		}
		else {
			throw new CheckoutException('This payment method does not exists');
		}
	}

	/**
	 * @return \Phalconmerce\Models\Popo\Abstracts\AbstractCountry
	 */
	protected function getCountryForCalculations() {
		// Most precise => shipping address
		$shippingAddress = $this->getShippingAddress();
		if (is_object($shippingAddress) && $shippingAddress->fk_country_id > 0) {
			return $shippingAddress->getCountry();
		}

		// Less precise => billing address
		$billingAddress = $this->getBillingAddress();
		if (is_object($billingAddress) && $billingAddress->fk_country_id > 0) {
			return $billingAddress->getCountry();
		}

		// Last case, no customer and no order, so default country from config
		return Di::getDefault()->get('config')->shop->default_country;
	}

	/**
	 * @return array
	 */
	protected function getDeliveryDelays() {
		$country = $this->getCountryForCalculations();
		if (is_object($country)) {
			// Get every DeliveryDelay for the shipping Address' country
			$results = array();
			/** @var \Phalcon\Mvc\Model\Resultset $deliveryDelaysList */
			$deliveryDelaysList = DeliveryDelayHasCountry::find(array(
				'status = 1 AND fk_country_id = :fk_country_id:',
				'bind' => array(
					'fk_country_id' => $country->id
				),
				'bindTypes' => array(
					'fk_country_id' => Column::BIND_PARAM_INT
				)
			));
			if (!empty($deliveryDelaysList) && $deliveryDelaysList->count() > 0) {
				/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractDeliveryDelayHasCountry $currentDeliveryDelayHasCountry */
				foreach ($deliveryDelaysList as $currentDeliveryDelayHasCountry) {
					/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractDeliveryDelay $currentDeliveryDelay */
					$currentDeliveryDelay = $currentDeliveryDelayHasCountry->getDeliveryDelay();
					$results[$currentDeliveryDelay->id] = $currentDeliveryDelay;
				}
			}
			return $results;
		}
	}

	/**
	 * @return \Phalconmerce\Models\Popo\Abstracts\AbstractOrder
	 */
	public function getOrder() {
		return $this->order;
	}

	/**
	 * @param \Phalconmerce\Models\Popo\Abstracts\AbstractOrder $order
	 * @return bool
	 */
	public function setOrder($order) {
		if (is_object($order) && is_a($order, '\Phalconmerce\Models\Popo\Abstracts\AbstractOrder')) {
			$this->order = $order;
			return true;
		}
		return false;
	}
}