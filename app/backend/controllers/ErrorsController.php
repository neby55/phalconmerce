<?php

namespace Backend\Controllers;

use Phalconmerce\Models\Utils;

class ErrorsController extends ControllerBase {

	public function initiliaze() {
		$this->view->setTemplateBefore('main_default');
	}

	public function show404Action() {
		$this->view->setTemplateBefore('main_default');
		$this->tag->setTitle('404');
	}

}

