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
		return Di::getDefault()->get('session')->get('backendUser');
	}

	/**
	 * @param $backendUser
	 * @return bool
	 */
	public function setConnectedUser($backendUser) {
		if (is_a($backendUser, '\Phalconmerce\Models\Popo\Abstracts\AbstractBackendUser')) {
			return Di::getDefault()->get('session')->get($this->backendUserSessionName);
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public static function disconnectUser() {
		Di::getDefault()->get('session')->remove('backendUser');
		return true;
	}

	public function generateMenu() {
		$this->menuItems =  array();

		$cmsMenuItems = new Menu($this->t('CMS'), 'edit', '');
		$cmsMenuItems->addSubMenu(new SubMenu($this->t('Blocks'), new MenuControllerIndexLink('cmsblock')));
		$cmsMenuItems->addSubMenu(new SubMenu($this->t('Content (banner)'), new MenuControllerIndexLink('cmscontentbanner')));
		$cmsMenuItems->addSubMenu(new SubMenu($this->t('Content (HTML)'), new MenuControllerIndexLink('cmscontenthtml')));
		$cmsMenuItems->addSubMenu(new SubMenu($this->t('Designs'), new MenuControllerIndexLink('cmsdesign')));
		$cmsMenuItems->addSubMenu(new SubMenu($this->t('Locations'), new MenuControllerIndexLink('cmslocation')));
		$cmsMenuItems->addSubMenu(new SubMenu($this->t('Pages'), new MenuControllerIndexLink('cmspage')));
		$this->menuItems[] = $cmsMenuItems;

		$productsMenuItems = new Menu($this->t('Products'), 'barcode', '');
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Attributes'), new MenuControllerIndexLink('attribute')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Attribute Groups'), new MenuControllerIndexLink('attributeset')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Categories'), new MenuControllerIndexLink('category')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Filters'), new MenuControllerIndexLink('filter')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Manufacturers'), new MenuControllerIndexLink('manufacturer')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Products'), new MenuControllerIndexLink('product')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Promotions'), new MenuControllerIndexLink('promotion')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Vounchers'), new MenuControllerIndexLink('vouncher')));
		$this->menuItems[] = $productsMenuItems;

		$productsMenuItems = new Menu($this->t('Sells'), 'money', '');
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Customers'), new MenuControllerIndexLink('customer')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Invoices'), new MenuControllerIndexLink('invoice')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Orders'), new MenuControllerIndexLink('order')));
		$productsMenuItems->addSubMenu(new SubMenu($this->t('Shipments'), new MenuControllerIndexLink('shipment')));
		$this->menuItems[] = $productsMenuItems;

		$settingsMenuItems = new Menu($this->t('Settings'), 'gears', '');
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Countries'), new MenuControllerIndexLink('country')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Currencies'), new MenuControllerIndexLink('currency')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Expeditors'), new MenuControllerIndexLink('expeditor')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Languages'), new MenuControllerIndexLink('lang')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('States'), new MenuControllerIndexLink('state')));
		$settingsMenuItems->addSubMenu(new SubMenu($this->t('Tax'), new MenuControllerIndexLink('tax')));
		$this->menuItems[] = $settingsMenuItems;
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
			2 => '<span class="btn btn-danger btn-circle"><i class="fa fa-times"></i></span>',
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
		if (array_key_exists($str, $this->translationsList)) {
			return $this->translationsList[$str];
		}
		return $str;
	}
}