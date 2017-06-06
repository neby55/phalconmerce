<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Services\Routes;

use Phalcon\Di;
use Phalcon\Mvc\Router\Group as RouterGroup;

class AdminRouterGroup extends RouterGroup {
	public function setPrefix($prefix) {
		parent::setPrefix(Di::getDefault()->get('configPhalconmerce')->adminDir.'/'.$prefix);
	}
}