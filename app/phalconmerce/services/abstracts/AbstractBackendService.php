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
use Backend\Models\Menu;
use Backend\Models\MenuControllerIndexLink;
use Backend\Models\MenuNamedRouteLink;
use Backend\Models\SubMenu;
use Phalconmerce\Models\Utils;

abstract class AbstractBackendService extends MainService {
	/** @var string */
	protected $backendUserSessionName;

	/** @var string[] */
	protected $breadcrumbsList;

	/** @var \Backend\Models\Menu[] */
	protected $menuItems;

	/** @var string[] */
	protected $translationsList;

	public function __construct() {
		$this->backendUserSessionName = 'backendUser';
		$this->breadcrumbsList = array();
		$this->menuItems = array();

		// And now generate contents
		$this->loadTranslations();
		$this->generateMenu();
		$this->generateBreadcrumbs();
	}

	/**
	 * @param string $label
	 * @param string $link
	 */
	protected function addBreadcrumb($label, $link) {
		$this->breadcrumbsList[$label] = $link;
	}

	/**
	 * @return string[]
	 */
	public function getBreadcrumbs() {
		return $this->breadcrumbsList;
	}

	public function generateBreadcrumbs() {
		if (is_array($this->menuItems)) {
			foreach ($this->menuItems as $currentMenuItem) {
				if ($currentMenuItem->isActive()) {
					if ($currentMenuItem->hasSubMenu()) {
						$this->addBreadcrumb($currentMenuItem->getLabel(), '');
						foreach ($currentMenuItem->getSubMenuList() as $currentSubMenuItem) {
							if ($currentSubMenuItem->isActive()) {
								$this->addBreadcrumb($currentSubMenuItem->getLabel(), $currentSubMenuItem->getLink()->getURL());
							}
						}
					}
					else {
						$this->addBreadcrumb($currentMenuItem->getLabel(), $currentMenuItem->getLink()->getURL());
					}
				}
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getConnectedUser() {
		$backendUser = Di::getDefault()->get('session')->get($this->backendUserSessionName);
		if (is_a($backendUser, '\Phalconmerce\Models\Popo\Abstracts\AbstractBackendUser')) {
			return $backendUser;
		}
		return false;
	}

	/**
	 * @param $backendUser
	 * @return bool
	 */
	public function setConnectedUser($backendUser) {
		if (is_a($backendUser, '\Phalconmerce\Models\Popo\Abstracts\AbstractBackendUser')) {
			Di::getDefault()->get('session')->set($this->backendUserSessionName, $backendUser);
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function disconnectUser() {
		Di::getDefault()->get('session')->remove($this->backendUserSessionName);
		return true;
	}

	public function generateMenu() {
		$this->menuItems =  array();

		$cmsMenuItems = new Menu($this->t('CMS'), 'edit', '');
		$cmsMenuItems->addSubMenu(new SubMenu($this->t('Blocks'), new MenuControllerIndexLink('cms_block')));
		$cmsMenuItems->addSubMenu(new SubMenu($this->t('Pages'), new MenuControllerIndexLink('cms_page')));
		$cmsMenuItems->addSubMenu(new SubMenu($this->t('Menu Groups'), new MenuControllerIndexLink('menu_group')));
		$cmsMenuItems->addSubMenu(new SubMenu($this->t('Menus'), new MenuControllerIndexLink('menu')));
		$this->menuItems['cms'] = $cmsMenuItems;

		$productsMenuItems = new Menu($this->t('Products'), 'barcode', '');
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Attributes'), new MenuControllerIndexLink('attribute')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Attribute Groups'), new MenuControllerIndexLink('attribute_set')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Categories'), new MenuControllerIndexLink('category')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Filters'), new MenuControllerIndexLink('filter')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Manufacturers'), new MenuControllerIndexLink('manufacturer')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Products'), new MenuControllerIndexLink('product')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Promotions'), new MenuControllerIndexLink('promotion')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Vouchers'), new MenuControllerIndexLink('voucher')));
		$this->menuItems['products'] = $productsMenuItems;

		$productsMenuItems = new Menu($this->t('Sells'), 'money', '');
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Customers'), new MenuControllerIndexLink('customer')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Invoices'), new MenuControllerIndexLink('invoice')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Orders'), new MenuControllerIndexLink('order')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Shipments'), new MenuControllerIndexLink('shipment')));
		$this->menuItems['sells'] = $productsMenuItems;

		$settingsMenuItems = new Menu($this->t('Settings'), 'gears', '');
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Countries'), new MenuControllerIndexLink('country')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Currencies'), new MenuControllerIndexLink('currency')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Deliveries'), new MenuControllerIndexLink('delivery_delay')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Expeditors'), new MenuControllerIndexLink('expeditor')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Languages'), new MenuControllerIndexLink('lang')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Options'), new MenuControllerIndexLink('shop_option')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Payment Methods'), new MenuControllerIndexLink('payment_method')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('States'), new MenuControllerIndexLink('state')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Stores'), new MenuControllerIndexLink('store')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Tax'), new MenuControllerIndexLink('tax')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Transactional Emails'), new MenuControllerIndexLink('transactional_email')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Translations'), new MenuControllerIndexLink('translation')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Users'), new MenuControllerIndexLink('backend_user')));
		$this->menuItems['settings'] = $settingsMenuItems;
	}

	/**
	 * @return \Backend\Models\Menu[]
	 */
	public function getMenu() {
		return $this->menuItems;
	}

	/**
	 * @return array
	 */
	public static function getBackendListStatusValues() {
		return array(
			0 => '<span class="btn btn-default btn-circle"><i class="fa fa-question"></i></span>',
			1 => '<span class="btn btn-success btn-circle"><i class="fa fa-check"></i></span>',
			2 => '<span class="btn btn-default btn-circle"><i class="fa fa-times"></i></span>',
		);
	}

	private function loadTranslations() {
		$filename = APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR . 'backend.csv';

		if (file_exists($filename)) {
			if (($fp = fopen($filename, "r")) !== false) {
				$firstLine = fgets($fp, 1024);
				while (($data = fgetcsv($fp, 1024, ";")) !== false) {
					$this->translationsList[$data[0]] = $data[1];
				}
				fclose($fp);
			}
		}
	}

	/**
	 * @param string $str
	 * @return string
	 */
	public function t($str) {
		if (is_array($this->translationsList) && array_key_exists($str, $this->translationsList)) {
			return $this->translationsList[$str];
		}
		return $str;
	}
}