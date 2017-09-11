<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Services\Abstracts;

use Backend\Models\Menu;
use Backend\Models\MenuControllerIndexLink;
use Backend\Models\MenuNamedRouteLink;
use Backend\Models\SubMenu;

abstract class AbstractBackendService extends MainService {
	public function getMenu() {
		$menuItems =  array();

		// Use Menu, MenuLink & SubMenu
		$menuItems[] = new Menu('Home', 'home', new MenuNamedRouteLink('backend-index'));

		$cmsMenuItems = new Menu('CMS', 'edit', '');
		$cmsMenuItems->addSubMenu(new SubMenu('Pages', new MenuControllerIndexLink('cmspage')));
		$cmsMenuItems->addSubMenu(new SubMenu('Blocks', new MenuControllerIndexLink('cmsblock')));
		$cmsMenuItems->addSubMenu(new SubMenu('Designs', new MenuControllerIndexLink('cmsdesign')));
		$menuItems[] = $cmsMenuItems;

		$productsMenuItems = new Menu('Products', 'barcode', '');
		$productsMenuItems->addSubMenu(new SubMenu('Attributes', new MenuControllerIndexLink('attribute')));
		$productsMenuItems->addSubMenu(new SubMenu('Attribute Groups', new MenuControllerIndexLink('attributeset')));
		$productsMenuItems->addSubMenu(new SubMenu('Categories', new MenuControllerIndexLink('category')));
		$productsMenuItems->addSubMenu(new SubMenu('Filters', new MenuControllerIndexLink('filter')));
		$productsMenuItems->addSubMenu(new SubMenu('Manufacturers', new MenuControllerIndexLink('manufacturer')));
		$productsMenuItems->addSubMenu(new SubMenu('Products', new MenuControllerIndexLink('product')));
		$productsMenuItems->addSubMenu(new SubMenu('Promotions', new MenuControllerIndexLink('promotion')));
		$productsMenuItems->addSubMenu(new SubMenu('Vounchers', new MenuControllerIndexLink('vouncher')));
		$menuItems[] = $productsMenuItems;

		$productsMenuItems = new Menu('Sells', 'money', '');
		$productsMenuItems->addSubMenu(new SubMenu('Customers', new MenuControllerIndexLink('customer')));
		$productsMenuItems->addSubMenu(new SubMenu('Invoices', new MenuControllerIndexLink('invoice')));
		$productsMenuItems->addSubMenu(new SubMenu('Orders', new MenuControllerIndexLink('order')));
		$productsMenuItems->addSubMenu(new SubMenu('Shipments', new MenuControllerIndexLink('shipment')));
		$menuItems[] = $productsMenuItems;

		$settingsMenuItems = new Menu('Settings', 'gears', '');
		$settingsMenuItems->addSubMenu(new SubMenu('Countries', new MenuControllerIndexLink('country')));
		$settingsMenuItems->addSubMenu(new SubMenu('Currencies', new MenuControllerIndexLink('currency')));
		$settingsMenuItems->addSubMenu(new SubMenu('Expeditors', new MenuControllerIndexLink('expeditor')));
		$settingsMenuItems->addSubMenu(new SubMenu('Languages', new MenuControllerIndexLink('lang')));
		$settingsMenuItems->addSubMenu(new SubMenu('States', new MenuControllerIndexLink('state')));
		$settingsMenuItems->addSubMenu(new SubMenu('Tax', new MenuControllerIndexLink('tax')));
		$menuItems[] = $settingsMenuItems;

		return $menuItems;
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
}