<?php

namespace Backend\Controllers;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller {
	public function initialize() {
		$this->tag->prependTitle($this->getDI()->get('config')->adminTitle.' | ');
		$this->view->setTemplateAfter('main');
	}
}
