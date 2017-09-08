<?php

namespace Backend\Controllers\Abstracts;

use Phalconmerce\Models\Utils;
use Backend\Controllers\ControllerBase;

abstract class AbstractErrorsController extends ControllerBase {

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

