<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Frontend\Controllers;

use Phalconmerce\Models\Utils;
use Phalconmerce\Models\Popo\Url;

class UrlController extends ControllerBase {
	public function dispatcherAction() {
		$urlObject = $this->dispatcher->getParam('url');
		Utils::debug($urlObject);

	}
}