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
use Phalconmerce\Models\Generic\Route as MyAccountRoute;
use Phalconmerce\Models\Popo\Order;

class AbstractMyAccountService extends LoginService {

	// Constants defining my-account routes' name (can be overridden)
	const MYACCOUNT_URL_FOLDER = '/my-account';
	const MYACCOUNT_SIGNIN_ROUTE_NAME = 'myaccount_signin';
	const MYACCOUNT_SIGNOUT_ROUTE_NAME = 'myaccount_signout';
	const MYACCOUNT_FORGOTPASSWORD_ROUTE_NAME = 'myaccount_forgotpassword';
	const MYACCOUNT_RESETPASSWORD_ROUTE_NAME = 'myaccount_resetpassword';
	const MYACCOUNT_INDEX_ROUTE_NAME = 'myaccount_index';

	// Method called inside the CheckoutController
	public function initMyAccount() {
		// Retrieve the customer in SESSION and create a new one
		$this->initLogin();
	}

	/**
	 * @return \Phalconmerce\Models\Generic\Abstracts\AbstractRoute[]
	 */
	public static function getRoutes() {
		$routesList = array();

		// Signin
		$routesList[] = new MyAccountRoute(
			static::MYACCOUNT_URL_FOLDER.'/signin',
			'MyAccount',
			'signin',
			static::MYACCOUNT_SIGNIN_ROUTE_NAME
		);
		// Disconnect
		$routesList[] = new MyAccountRoute(
			static::MYACCOUNT_URL_FOLDER.'/disconnect',
			'MyAccount',
			'signout',
			static::MYACCOUNT_SIGNOUT_ROUTE_NAME
		);
		// ForgotPassword
		$routesList[] = new MyAccountRoute(
			static::MYACCOUNT_URL_FOLDER.'/forgot-password',
			'MyAccount',
			'forgotPassword',
			static::MYACCOUNT_FORGOTPASSWORD_ROUTE_NAME
		);
		// ResetPassword
		$routesList[] = new MyAccountRoute(
			static::MYACCOUNT_URL_FOLDER.'/reset-password/([0-9a-z]{32})',
			'MyAccount',
			'resetPassword',
			static::MYACCOUNT_RESETPASSWORD_ROUTE_NAME,
			array(
				'token' => 1
			)
		);
		// Index
		$routesList[] = new MyAccountRoute(
			static::MYACCOUNT_URL_FOLDER.'/',
			'MyAccount',
			'index',
			static::MYACCOUNT_INDEX_ROUTE_NAME
		);

		return $routesList;
	}

	/**
	 * @return string
	 */
	public static function getSigninRouteName() {
		return static::MYACCOUNT_SIGNIN_ROUTE_NAME;
	}

	/**
	 * @return string
	 */
	public static function getSignoutRouteName() {
		return static::MYACCOUNT_SIGNOUT_ROUTE_NAME;
	}

	/**
	 * @return string
	 */
	public static function getForgotPasswordRouteName() {
		return static::MYACCOUNT_FORGOTPASSWORD_ROUTE_NAME;
	}

	/**
	 * @return string
	 */
	public static function getResetPasswordRouteName() {
		return static::MYACCOUNT_RESETPASSWORD_ROUTE_NAME;
	}

	/**
	 * @return string
	 */
	public static function getIndexRouteName() {
		return static::MYACCOUNT_INDEX_ROUTE_NAME;
	}

	public function getOrders() {
		if ($this->isCustomerConnected()) {
			/** @var \Phalcon\Mvc\Model\Resultset $ordersList */
			$ordersList = Order::find(array(
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
}