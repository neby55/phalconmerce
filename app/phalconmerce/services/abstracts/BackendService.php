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
use Backend\Models\SubMenu;

abstract class BackendService extends MainService {
	public function getMenu() {
		$menuItems =  array();

		// TODO define here Menu labels and links (without baseURL, and without the first "/")
		// Use Menu, MenuLink & SubMenu
		$menuItems[] = new Menu('Home', 'dashboard', '');

		$productsMenuItems = new Menu('Products', 'barcode', '');
		$productsMenuItems->addSubMenu(new SubMenu('First product type', 'first-type'));
		$productsMenuItems->addSubMenu(new SubMenu('Second product type', 'second-type'));

		$menuItems[] = $productsMenuItems;

		return $menuItems;
	}
}