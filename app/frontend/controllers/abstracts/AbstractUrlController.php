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
		/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractUrl $urlObject */
		$urlObject = $this->dispatcher->getParam('url');

		// Defines META
		$this->getDI()->get('frontendService')->setMetaTitle($urlObject->metaTitle);
		$this->getDI()->get('frontendService')->setMetaDescription($urlObject->metaDescription);
		$this->getDI()->get('frontendService')->setMetaKeywords($urlObject->metaKeywords);

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