<?php

namespace Backend\Controllers;

use Phalconmerce\Models\Utils;

class ErrorsController extends ControllerBase {

	public function initiliaze() {
		$this->view->setTemplateBefore('main_default');
	}

	public function show404Action() {
		$this->view->setTemplateBefore('main_default');
		$this->tag->setTitle('Not found');
	}

	public function show403Action() {
		$this->view->setTemplateBefore('main_default');
		$this->tag->setTitle('Forbidden');
	}

	public function show500Action() {
		$this->view->setTemplateBefore('main_default');
		$this->tag->setTitle('Internal server error');
	}

}

