<?php

namespace Frontend\Controllers\Abstracts;

use Phalconmerce\Models\Utils;
use Frontend\Controllers\ControllerBase;

abstract class AbstractErrorsController extends ControllerBase {

	public function initiliaze() {
	}

	public function show404Action() {
		$this->tag->setTitle('Not found');
	}

	public function show403Action() {
		$this->tag->setTitle('Forbidden');
	}

	public function show500Action() {
		$this->tag->setTitle('Internal server error');
	}

}

