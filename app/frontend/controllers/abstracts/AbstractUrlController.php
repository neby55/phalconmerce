<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Frontend\Controllers\Abstracts;

use Phalconmerce\Models\Utils;
use Frontend\Controllers\ControllerBase;

class AbstractUrlController extends ControllerBase {
	public function dispatcherAction() {
		$urlObject = $this->dispatcher->getParam('url');

		$this->dispatcher->forward(
			[
				"controller" => $urlObject->entity,
				"action" => "index",
				"params" => array(
					'id' => $urlObject->entityId
				),
			]
		);
		return false;
	}
}