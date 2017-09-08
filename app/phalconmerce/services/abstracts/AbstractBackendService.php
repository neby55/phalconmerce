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

		$productsMenuItems = new Menu('CMS', 'edit', '');
		$productsMenuItems->addSubMenu(new SubMenu('Pages', new MenuControllerIndexLink('cmspage')));
		$productsMenuItems->addSubMenu(new SubMenu('Blocks', new MenuControllerIndexLink('cmsblock')));
		$productsMenuItems->addSubMenu(new SubMenu('Designs', new MenuControllerIndexLink('cmsdesign')));

		$menuItems[] = $productsMenuItems;

		return $menuItems;
	}
}