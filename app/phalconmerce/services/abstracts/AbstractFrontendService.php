<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Services\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\Breadcrumb;
use Phalconmerce\Models\Design;
use Phalconmerce\Models\Popo\Url;
use Phalconmerce\Models\Popo\CmsBlock;
use Phalconmerce\Models\Popo\ShopOption;
use Phalconmerce\Models\Utils;

abstract class AbstractFrontendService extends MainService {
	/** @var string */
	protected $baseURL;
	/** @var string */
	protected $absoluteURL;
	/** @var string */
	protected $absoluteBaseUri;
	/** @var string */
	protected $currentURL;
	/** @var string */
	protected $metaTitle;
	/** @var string */
	protected $metaDescription;
	/** @var string */
	protected $metaKeywords;
	/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCart[] */
	protected $cart;
	/** @var float */
	protected $cartProductsTotalVatExcluded;
	/** @var float */
	protected $cartProductsTotalVatIncluded;
	/** @var float */
	protected $cartSubTotalVatExcluded;
	/** @var float */
	protected $cartSubTotalVatIncluded;
	/** @var float */
	protected $cartTaxTotal;
	/** @var float */
	protected $cartShipping;
	/** @var float */
	protected $cartTotal;
	/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractVoucher */
	protected $voucher;
	/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractDeliveryDelay */
	protected $shipping;
	/** @var Breadcrumb[] */
	protected $breadcrumbs;
	/** @var string[] */
	protected $shopOptionList;

	public static $shopTitle;
	public static $defaultCurrency;
	public static $defaultLangId;
	public static $cookieLangName;
	public static $cookieCurrencyName;
	public static $cookiesLifetimeInDays;
	public static $dateFormat;
	public static $freeShippingAmountVatIncluded;

	// Used in setupFromShopOption method
	public static $shopOptionDefaultValues = array(
		ShopOption::NAME_IS_WEBSITE_ACTIVE => '1',
		ShopOption::NAME_IS_SHOP_ACTIVE => '1',
		ShopOption::NAME_IS_PO_INDEX_ACTIVE => '0',
		ShopOption::NAME_FREE_SHIPPING_AMOUNT => '0'
	);

	// The route name for the checkout index configured in app/frontend/routes.php
	const CHECKOUT_INDEX_ROUTE_NAME = 'checkout_index';

	public function __construct() {
		// TODO retriveve infos to complete properties
		$this->baseURL = Di::getDefault()->get('url')->getBaseUri();
		$this->currentURL = Di::getDefault()->get('router')->getRewriteUri();
		$this->absoluteBaseUri = (isset($_SERVER['https']) ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'];
		$this->absoluteURL = $this->absoluteBaseUri.$this->currentURL;
		$this->metaTitle = '';
		$this->metaDescription = '';
		$this->metaKeywords = '';
		$this->cart = array();
		$this->cartSubTotalVatExcluded = 0;
		$this->cartSubTotalVatIncluded = 0;
		$this->cartShipping = 0;
		$this->cartTaxTotal = 0;
		$this->cartTotal = 0;
		$this->breadcrumbs = array();
		$this->shopOptionList = array();

		// Setting up data from shopOptions
		$this->loadShopOptions();
		// Setting up data
		$this->setup();
		// Load voucher from session
		$this->loadVoucher();
		// Load Shipping from session
		$this->loadShipping();
		// Load Cart from session
		$this->loadCart();
	}

	protected function setup() {
		/** @var \Phalcon\Config $config */
		$config = Di::getDefault()->get('config');
		// Setting static values
		self::$shopTitle = $this->getOption('shop-title');
		self::$defaultCurrency = $this->getOption('shop-default_currency');
		self::$defaultLangId = \Phalconmerce\Services\TranslationService::getLangIdFromLangCode($this->getOption('shop-default_lang'));
		self::$cookieLangName = $this->getOption('shop-cookie_lang_name');
		self::$cookieCurrencyName = $this->getOption('shop-cookie_currency_name');
		self::$cookiesLifetimeInDays = $this->getOption('shop-cookies_lifetime_in_days');
		self::$dateFormat = $this->getOption('shop-date_format');
		self::$freeShippingAmountVatIncluded = $this->getOption(ShopOption::NAME_FREE_SHIPPING_AMOUNT);
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function getOption($key) {
		/** @var \Phalcon\Config $config */
		$config = Di::getDefault()->get('config');

		// First, shopOptions
		if (array_key_exists($key, $this->shopOptionList)) {
			return $this->shopOptionList[$key];
		}

		// From config
		if (strpos($key, '-') !== false) {
			$subConfig = explode('-', $key);
			for ($i=0;$i < count($subConfig); $i++) {
				$currentSubConfigName = $subConfig[$i];
				if (isset($config->$currentSubConfigName)) {
					$config = $config->$currentSubConfigName;
				}
				else if (isset($config[$currentSubConfigName])) {
					$config = $config[$currentSubConfigName];
				}
				else {
					Di::getDefault()->get('logger')->notice('FrontendService::getOption() :: key "'.$key.'" can\'t be reached => $config->'.$currentSubConfigName.' or $config['.$currentSubConfigName.'] do not exists');
					return false;
				}
			}
			return $config;
		}
	}

	protected function loadShopOptions() {
		// Insert default values
		$this->shopOptionList = self::$shopOptionDefaultValues;

		// Get shop options from DB
		$result = ShopOption::find();
		if (!empty($result) && $result->count() > 0) {
			/** @var ShopOption $currentShopOption */
			foreach ($result as $currentShopOption) {
				$this->shopOptionList[$currentShopOption->name] = $currentShopOption->value;
			}
		}

		// If Website enabled by IP adresses
		if (isset($this->shopOptionList[ShopOption::NAME_IS_WEBSITE_ACTIVE]) && $this->shopOptionList[ShopOption::NAME_IS_WEBSITE_ACTIVE] == 0 && array_key_exists(ShopOption::NAME_ENABLE_WEBSITE_BY_IP, $this->shopOptionList)) {
			if (strpos($this->shopOptionList[ShopOption::NAME_ENABLE_WEBSITE_BY_IP], ',')) {
				$ipAdresses = explode(',', $this->shopOptionList[ShopOption::NAME_ENABLE_WEBSITE_BY_IP]);
			}
			else {
				$ipAdresses = array($this->shopOptionList[ShopOption::NAME_ENABLE_WEBSITE_BY_IP]);
			}
			if (!empty($ipAdresses) && is_array($ipAdresses)) {
				$userIpAdress = self::getUserIpAdress();
				foreach ($ipAdresses as $currentIpAdress) {
					if ($userIpAdress == $currentIpAdress) {
						$this->shopOptionList[ShopOption::NAME_IS_WEBSITE_ACTIVE] = 1;
					}
				}
			}
		}

		// If Shop enabled by IP adresses
		if (isset($this->shopOptionList[ShopOption::NAME_IS_SHOP_ACTIVE]) && $this->shopOptionList[ShopOption::NAME_IS_SHOP_ACTIVE] == 0 && array_key_exists(ShopOption::NAME_ENABLE_SHOP_BY_IP, $this->shopOptionList)) {
			if (strpos($this->shopOptionList[ShopOption::NAME_ENABLE_SHOP_BY_IP], ',')) {
				$ipAdresses = explode(',', $this->shopOptionList[ShopOption::NAME_ENABLE_SHOP_BY_IP]);
			}
			else {
				$ipAdresses = array($this->shopOptionList[ShopOption::NAME_ENABLE_SHOP_BY_IP]);
			}
			if (!empty($ipAdresses) && is_array($ipAdresses)) {
				$userIpAdress = self::getUserIpAdress();
				foreach ($ipAdresses as $currentIpAdress) {
					if ($userIpAdress == $currentIpAdress) {
						$this->shopOptionList[ShopOption::NAME_IS_SHOP_ACTIVE] = 1;
					}
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public static function getUserIpAdress() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	/**
	 * @param string $str
	 * @param array $data
	 * @return string
	 */
	public function e($str, $data=array()) {
		return $this->getDI()->get('translation')->e($str, $data);
	}

	/**
	 * @param string $str
	 * @param array $data
	 * @return string
	 */
	public function t($str, $data=array()) {
		return $this->getDI()->get('translation')->t($str, $data);
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @param int $duration
	 * @return bool
	 */
	public function addCookie($name, $value, $duration) {
		// Handle if $duration is expire timestamp
		$expire = $duration > time() ? $duration : time() + $duration;
		$this->getDI()->get('cookies')->set($name, $value, $expire, $this->getBaseURL());
		return true;
	}

	/**
	 * @param string $slug
	 * @return string
	 */
	public function showCmsBlock($slug) {
		$object = CmsBlock::findFirst(array(
			'slug = :slug: AND status = 1 AND fk_lang_id = :lang_id:',
			'bind' => array(
				'slug' => $slug,
				'lang_id' => $this->getDI()->get('translation')->getLangId()
			)
		));
		if (!empty($object) && is_object($object)) {
			return $object->html;
		}
		return '';
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function addBaseUriIfNeeded($url) {
		// If external URL
		if (preg_match('/^https?:/i', $url) == 1 || substr($url,0,2) == '//') {
			return $url;
		}
		return $this->getDI()->get('url')->getBaseUri().$url;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function applyCloudinaryTransformation($url, $transformationName) {
		// If cloudinary URL
		if (strpos($url, '//res.cloudinary.com/') !== false) {
			return str_replace('/image/upload/', '/image/upload/t_'.$transformationName.'/', $url);
		}
		return $this->addBaseUriIfNeeded($url);
	}

	/**
	 * @param float $price
	 * @return string
	 */
	public function formatPriceVatInc($price) {
		return $this->formatPrice($price * $this->getVatRatio());
	}

	/**
	 * @param float $price
	 * @return string
	 */
	public function formatPriceVatExc($price) {
		return $this->formatPrice($price);
	}

	/**
	 * @param float $price
	 * @return string
	 */
	public function formatPrice($price) {
		// If decimals
		if (floor($price) != $price) {
			return number_format($price, 2, ',', ' ').' '.$this->getDI()->get('translation')->getCurrencySigle();
		}
		else {
			return $price.' '.$this->getDI()->get('translation')->getCurrencySigle();
		}
	}

	/**
	 * @param string $mysqlDate
	 * @return string
	 */
	public function formatDate($mysqlDate) {
		if (!empty($mysqlDate)) {
			$timestamp = strtotime($mysqlDate);
			return date(self::$dateFormat, $timestamp);
		}
		return false;
	}

	public function cartTotals() {
		// Initializations
		$this->cartSubTotalVatExcluded = 0;
		$this->cartSubTotalVatIncluded = 0;
		$this->cartProductsTotalVatExcluded = 0;
		$this->cartProductsTotalVatIncluded = 0;
		$this->cartShipping = 0;
		$this->cartTaxTotal = 0;
		$this->cartTotal = 0;

		// Additions from current cart
		if (is_array($this->cart)) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCart $currentCart */
			foreach ($this->cart as $currentCart) {
				$this->cartProductsTotalVatExcluded += $currentCart->getTotalVatExcluded();
				$this->cartProductsTotalVatIncluded += $currentCart->getTotalVatIncluded();
				$this->cartTaxTotal += $currentCart->getTotalTax();
			}
		}

		// SubTotals
		$this->cartSubTotalVatIncluded = $this->cartProductsTotalVatIncluded;
		$this->cartSubTotalVatExcluded = $this->cartProductsTotalVatExcluded;

		// Shipping
		if (isset($this->shipping) && is_object($this->shipping)) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractExpeditor $expeditor */
			$expeditor = $this->shipping->getExpeditor();

			if (is_object($expeditor)) {
				// Get shipping amount VAT included
				$this->cartShipping = $expeditor->amountVatExcluded * $this->getVatRatio();

				// Adding VAT
				$this->cartTaxTotal += $this->cartShipping - $expeditor->amountVatExcluded;
			}
		}

		// If Voucher in session
		if ($this->getVoucherId() > 0) {
			$this->cartSubTotalVatExcluded -= $this->getVoucherAmountVatExcluded();
			$this->cartSubTotalVatIncluded -= $this->getVoucherAmountVatIncluded();
			$this->cartTaxTotal = $this->cartSubTotalVatIncluded - $this->cartSubTotalVatExcluded;
		}

		// Total at the end
		$this->cartTotal = $this->cartSubTotalVatIncluded + $this->cartShipping;
	}

	public function loadCart() {
		if (Di::getDefault()->get('session')->has('cart')) {
			$cartTmp = Di::getDefault()->get('session')->get('cart');
			if (is_array($cartTmp)) {
				foreach ($cartTmp as $currentCartLine) {
					$this->addCart($currentCartLine, false);
					//Utils::debug($currentCartLine);
				}
				//exit;
			}
		}
	}

	public function saveCart() {
		Di::getDefault()->get('session')->set('cart', $this->cart);
	}

	public function removeCart() {
		Di::getDefault()->get('session')->remove('cart');
	}

	public function saveCartToDb($orderId) {
		// First save each line to DB
		/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCart $currentCartLine */
		foreach ($this->cart as $currentIndex=>$currentCartLine) {
			$this->cart[$currentIndex]->fk_order_id = $orderId;
			$this->cart[$currentIndex]->save();
		}
		// Then save current cart (with orderId) to DB
		$this->saveCart();
	}

	/**
	 * @param \Phalconmerce\Models\Popo\Abstracts\AbstractCart $cart
	 * @param bool $saveImmediatly
	 * @return bool
	 */
	public function addCart($cart, $saveImmediatly=true) {
		if (is_a($cart, '\Phalconmerce\Models\Popo\Abstracts\AbstractCart')) {
			// If already in cart
			if (array_key_exists($cart->fk_product_id, $this->cart)) {
				// Adding requested qty to existing cart line
				$this->cart[$cart->fk_product_id]->quantity += $cart->quantity;
			}
			else {
				$this->cart[$cart->fk_product_id] = $cart;
			}

			if ($saveImmediatly) {
				$this->saveCart();
			}
			// Update totals
			$this->cartTotals();
			return true;
		}
		return false;
	}

	/**
	 * @param mixed $index
	 * @return bool
	 */
	public function deleteCart($index) {
		$deleted = false;
		if (is_array($this->cart)) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCart $currentCart */
			foreach ($this->cart as $currentIndex=>$currentCart) {
				if ($currentIndex == $index) {
					unset($this->cart[$currentIndex]);
					$deleted = true;
				}
			}
		}
		if ($deleted) {
			$this->saveCart();
			$this->cartTotals();
			return true;
		}

		return false;
	}

	/**
	 * @param mixed $index
	 * @param int $newQty
	 * @return bool
	 */
	public function changeQtyCart($index, $newQty) {
		$updated = false;
		if (is_array($this->cart)) {
			/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractCart $currentCart */
			foreach ($this->cart as $currentIndex=>$currentCart) {
				if ($currentIndex == $index) {
					$this->cart[$currentIndex]->quantity = $newQty;
					$updated = true;
				}
			}
		}
		if ($updated) {
			$this->saveCart();
			$this->cartTotals();
			return true;
		}

		return false;
	}

	/**
	 * Load voucher Object from session, if exists
	 */
	public function loadVoucher() {
		if (Di::getDefault()->get('session')->has('voucher')) {
			$voucherTmp = Di::getDefault()->get('session')->get('voucher');
			if (is_a($voucherTmp, '\\Phalconmerce\\Models\\Popo\\Abstracts\\AbstractVoucher')) {
				$this->voucher = $voucherTmp;
			}
		}
	}

	/**
	 * Save voucher Object on session, if exists
	 */
	public function saveVoucher() {
		if (!empty($this->voucher)) {
			Di::getDefault()->get('session')->set('voucher', $this->voucher);
		}
	}

	/**
	 * Delete voucher Object on session, if exists
	 */
	public function deleteVoucher() {
		if (!empty($this->voucher)) {
			Di::getDefault()->get('session')->remove('voucher');
		}
	}

	/**
	 * Load DeliveryDelay Method Object from session, if exists
	 */
	public function loadShipping() {
		if (Di::getDefault()->get('session')->has('shipping')) {
			$shippingTmp = Di::getDefault()->get('session')->get('shipping');
			if (is_a($shippingTmp, '\\Phalconmerce\\Models\\Popo\\Abstracts\\AbstractDeliveryDelay')) {
				$this->shipping = $shippingTmp;
			}
		}
	}

	/**
	 * Save DeliveryDelay Object on session, if exists
	 */
	public function saveShipping() {
		if (!empty($this->shipping)) {
			Di::getDefault()->get('session')->set('shipping', $this->shipping);
		}
	}

	/**
	 * Delete DeliveryDelay Object on session, if exists
	 */
	public function deleteShipping() {
		if (!empty($this->shipping)) {
			Di::getDefault()->get('session')->remove('shipping');
		}
	}

	/**
	 * @return float
	 */
	public function getVatRatio() {
		// TODO get the ratio from lang/country
		return 1.2;
	}

	/**
	 * @return string
	 */
	public function getCheckoutUrl() {
		return Di::getDefault()->get('url')->get(['for'=>static::CHECKOUT_INDEX_ROUTE_NAME]);
	}

	/**
	 * @param string $entity
	 * @param int $entityId
	 * @return string
	 */
	public function getEntityPermalink($entity, $entityId) {
		return Url::getEntityPermalink($entity, $entityId, $this->getDI()->get('translation')->getLangId());
	}

	/**
	 * @param Breadcrumb $breadcrumb
	 */
	public function addBreadcrumb(Breadcrumb $breadcrumb) {
		$this->breadcrumbs[] = $breadcrumb;
	}

	/**
	 * @return bool
	 */
	public static function isThereFreeShippingamount() {
		return (static::getFreeShippingAmount() > 0);
	}

	/**
	 * @return float
	 */
	public static function getFreeShippingAmount() {
		return (float) static::$freeShippingAmountVatIncluded;
	}

	/**
	 * @return string
	 */
	public function getBaseURL() {
		return $this->baseURL;
	}

	/**
	 * @return string
	 */
	public function getCurrentURL() {
		return $this->currentURL;
	}

	/**
	 * @return string
	 */
	public function getAbsoluteURL() {
		return $this->absoluteURL;
	}

	/**
	 * @return string
	 */
	public function getMetaTitle() {
		return $this->metaTitle;
	}

	/**
	 * @param string $metaTitle
	 */
	public function setMetaTitle($metaTitle) {
		$this->metaTitle = $metaTitle;
	}

	/**
	 * @return string
	 */
	public function getMetaDescription() {
		return $this->metaDescription;
	}

	/**
	 * @param string $metaDescription
	 */
	public function setMetaDescription($metaDescription) {
		$this->metaDescription = $metaDescription;
	}

	/**
	 * @return string
	 */
	public function getMetaKeywords() {
		return $this->metaKeywords;
	}

	/**
	 * @param string $metaKeywords
	 */
	public function setMetaKeywords($metaKeywords) {
		$this->metaKeywords = $metaKeywords;
	}

	/**
	 * @return mixed
	 */
	public static function getShopTitle() {
		return self::$shopTitle;
	}

	/**
	 * @return mixed
	 */
	public static function getDefaultCurrency() {
		return self::$defaultCurrency;
	}

	/**
	 * @return mixed
	 */
	public static function getDefaultLangId() {
		return self::$defaultLangId;
	}

	/**
	 * @return mixed
	 */
	public static function getCookieLangName() {
		return self::$cookieLangName;
	}

	/**
	 * @return mixed
	 */
	public static function getCookieCurrencyName() {
		return self::$cookieCurrencyName;
	}

	/**
	 * @return mixed
	 */
	public static function getCookiesLifetimeInDays() {
		return self::$cookiesLifetimeInDays;
	}

	/**
	 * @return string
	 */
	public function isWebsiteActive() {
		return intval($this->shopOptionList[ShopOption::NAME_IS_WEBSITE_ACTIVE]) == 1;
	}

	/**
	 * @return string
	 */
	public function isShopActive() {
		return intval($this->shopOptionList[ShopOption::NAME_IS_SHOP_ACTIVE]) == 1;
	}

	/**
	 * @return string
	 */
	public function isPoIndexActive() {
		return Di::getDefault()->get('config')->loadTranslationIndexes || isset($this->shopOptionList[ShopOption::NAME_IS_PO_INDEX_ACTIVE]) && intval($this->shopOptionList[ShopOption::NAME_IS_PO_INDEX_ACTIVE]) == 1;
	}

	/**
	 * @return \Phalconmerce\Models\Design[]
	 */
	public function getDesignsList() {
		return $this->designsList;
	}

	/**
	 * @return \Phalconmerce\Models\Breadcrumb[]
	 */
	public function getBreadcrumbs() {
		return $this->breadcrumbs;
	}

	/**
	 * @return string
	 */
	public function getAbsoluteBaseUri() {
		return $this->absoluteBaseUri;
	}

	/**
	 * @return \Phalconmerce\Models\Popo\Abstracts\AbstractCart[]
	 */
	public function getCart() {
		return $this->cart;
	}

	/**
	 * @return int
	 */
	public function getNbProductsInCart() {
		return sizeof($this->cart);
	}

	/**
	 * @return float
	 */
	public function getCartSubTotalVatExcluded() {
		return $this->cartSubTotalVatExcluded;
	}

	/**
	 * @return float
	 */
	public function getCartSubTotalVatIncluded() {
		return $this->cartSubTotalVatIncluded;
	}

	/**
	 * @return float
	 */
	public function getCartProductsTotalVatExcluded() {
		return $this->cartProductsTotalVatExcluded;
	}

	/**
	 * @return float
	 */
	public function getCartProductsTotalVatIncluded() {
		return $this->cartProductsTotalVatIncluded;
	}

	/**
	 * @return float
	 */
	public function getCartTaxTotal() {
		return $this->cartTaxTotal;
	}

	/**
	 * @return float
	 */
	public function getCartShipping() {
		return $this->cartShipping;
	}

	/**
	 * @return float
	 */
	public function setCartShipping($shippingAmount) {
		return $this->cartShipping;
	}

	/**
	 * @return float
	 */
	public function getCartTotal() {
		return $this->cartTotal;
	}

	/**
	 * @return \Phalconmerce\Models\Popo\Abstracts\AbstractVoucher
	 */
	public function getVoucher() {
		return $this->voucher;
	}

	/**
	 * @return int
	 */
	public function getVoucherId() {
		if (is_a($this->voucher, '\\Phalconmerce\\Models\\Popo\\Abstracts\\AbstractVoucher')) {
			return $this->voucher->id;
		}
		return 0;
	}

	/**
	 * @return int
	 */
	public function getVoucherAmountVatExcluded() {
		if (is_a($this->voucher, '\\Phalconmerce\\Models\\Popo\\Abstracts\\AbstractVoucher')) {
			return $this->voucher->getCartTotalReduction($this->getCartProductsTotalVatExcluded(), $this->getCartShipping());
		}
		return 0;
	}

	/**
	 * @return int
	 */
	public function getVoucherAmountVatIncluded() {
		if (is_a($this->voucher, '\\Phalconmerce\\Models\\Popo\\Abstracts\\AbstractVoucher')) {
			return $this->voucher->getCartTotalReduction($this->getCartProductsTotalVatExcluded(), $this->getCartShipping()) * $this->getVatRatio();
		}
		return 0;
	}

	/**
	 * @param \Phalconmerce\Models\Popo\Abstracts\AbstractVoucher $voucher
	 */
	public function setVoucher($voucher) {
		if (is_a($voucher, '\\Phalconmerce\\Models\\Popo\\Abstracts\\AbstractVoucher')) {
			$this->voucher = $voucher;
			// Totals
			$this->cartTotals();
			$this->saveCart();
		}
	}

	/**
	 * @return \Phalconmerce\Models\Popo\Abstracts\AbstractDeliveryDelay
	 */
	public function getShipping() {
		return $this->shipping;
	}

	/**
	 * @param \Phalconmerce\Models\Popo\Abstracts\AbstractDeliveryDelay $shipping
	 */
	public function setShipping($shipping) {
		if (is_a($shipping, '\\Phalconmerce\\Models\\Popo\\Abstracts\\AbstractDeliveryDelay')) {
			$this->shipping = $shipping;
			// Totals
			$this->cartTotals();
			$this->saveCart();
		}
	}

	/**
	 * To be override
	 * @return string
	 */
	public function getSearchUrl() {
		return '';
	}
}