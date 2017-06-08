<?php

namespace Backend\Controllers;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller {
	public function initialize() {
		$this->tag->prependTitle($this->getDI()->get('config')->adminTitle.' | ');
		$this->view->setVar('h1', 'Page Name');
		$this->view->setTemplateBefore('main_default');
	}
}
