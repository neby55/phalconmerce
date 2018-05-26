<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models\Checkout\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\Checkout\PaymentSystem;

abstract class AbstractBankTransfer extends PaymentSystem {
	public function __construct($paymentMethod) {
		parent::__construct($paymentMethod);

		$this->partialView = 'checkout/payment_bank_transfer';
	}
}