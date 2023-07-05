<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models\Checkout\Abstracts;

use Phalcon\Mvc\Router;
use Phalconmerce\Models\Exceptions\PaymentException;
use Phalconmerce\Models\Popo\PaymentMethod;
use Phalconmerce\Models\Popo\Payment;

/*
 * To define a new payment system, you can directly extends from this class
 * but you should extends your class to its child "PaymentSystem" where you can write your own code
 */
abstract class AbstractPaymentSystem {
	// TODO ajouter config property + const config file, si valeur => charger du fichier .conf
	/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractPaymentMethod */
	protected $paymentMethod;
	/** @var string */
	protected $partialView;
	/** @var bool */
	protected $external;
	/** @var array */
	protected $paymentParams;
	/** @var string */
	protected $routePaymentConfirmed;
	/** @var string */
	protected $routePaymentCanceled;
	/** @var string */
	protected $routePaymentRefused;

	public function __construct($paymentMethod) {
		$this->paymentMethod = $paymentMethod;
		$this->partialView = '';
		// Default route names
		// Replace these values in child "PaymentSystem"
		$this->routePaymentConfirmed = 'order-confirmed';
		$this->routePaymentCanceled = 'payment-canceled';
		$this->routePaymentRefused = 'payment-refused';
	}

	/**
	 * @return \Phalconmerce\Models\Popo\Abstracts\AbstractPaymentMethod
	 */
	public function getPaymentMethod() {
		return $this->paymentMethod;
	}

	/**
	 * @return string
	 */
	public function getPartialView() {
		return $this->partialView;
	}

	/**
	 * Method that permits "children" to define specific routes
	 * Phalconmerce URL System will automatically call this method
	 *      for every class related to an enabled PaymentMethod
	 * @return \Phalconmerce\Models\Generic\Abstracts\AbstractRoute[]
	 */
	public static function getRoutes() {
		return array();
	}

	/**
	 * Methods that permits "children" to save a payment in DB
	 *
	 * @param \Phalconmerce\Models\Popo\Abstracts\AbstractOrder $order
	 * @param float $amount
	 * @return bool
	 * @throws PaymentException
	 */
	protected function savePayment($order, $amount) {
		// If the order is submitted and is a right Order Class
		if (!empty($order) && is_a($order, '\Phalconmerce\Models\Popo\Abstracts\AbstractOrder')) {
			// If the payment method is submitted and is a right PaymentMethod Class
			if (!empty($this->paymentMethod) && is_a($this->paymentMethod, '\Phalconmerce\Models\Popo\Abstracts\AbstractPaymentMethod')) {
				// Check if this payment method is enabled
				if ($this->paymentMethod->status == 1) {
					// Several checks before saving payment
					// Amount valid
					if (!is_numeric($amount)) {
						throw new PaymentException('Amount is incorrect');
					}
					// Amount > 0 or free payment method
					if ($amount <= 0 && !$this->paymentMethod->type == PaymentMethod::TYPE_FREE) {
						throw new PaymentException('Amount is 0 for a non-free payment method');
					}
					// amount < order price
					if ($amount > $order->amountVatIncluded) {
						throw new PaymentException('Amount exceed order totals');
					}

					// Create the right Payment object
					/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractPayment $payment */
					$payment = new Payment();
					$payment->fk_order_id = $order->id;
					$payment->fk_payment_method_id = $this->paymentMethod->id;
					$payment->params = json_encode($this->paymentParams, JSON_PRETTY_PRINT);
					$payment->refundAmount = 0;
					$payment->paymentAmount = $amount;
					$payment->paymentDate = date('Y-m-d H:i:s');

					// If error during storing data in DB
					if (!$payment->save()) {
						throw new PaymentException('Error saving Payment in DB');
					}
					return true;
				}
				else {
					throw new PaymentException('This PaymentMethod is disabled');
				}
			}
			else {
				throw new PaymentException('Incorrect PaymentMethod');
			}
		}
		else {
			throw new PaymentException('Incorrect Order');
		}
	}
}