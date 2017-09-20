<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Frontend\Controllers\Abstracts;

use Phalcon\Mvc\Controller;

class AbstractControllerBase extends Controller {
	public function initialize() {
		$config = $this->getDI()->get('config');
		$this->view->setVar('config', $config);

		$this->tag->prependTitle($config->adminTitle.' | ');
		$this->setSubtitle('Page Name');

		// Disabling default validators requiring all fields to be filled
		\Phalcon\Mvc\Model::setup(array(
			'notNullValidations' => false
		));
	}

	public function setSubtitle($str) {
		$this->view->setVar('h1', $this->di->get('frontendService')->t($str));
	}
}