<?php

namespace Frontend\Controllers\Abstracts;

use Phalconmerce\Models\Utils;
use Frontend\Controllers\ControllerBase;

abstract class AbstractErrorsController extends ControllerBase {

	public function initiliaze() {
	}

	public function show404Action() {
		$this->tag->setTitle('Not found');
		$this->getDI()->get('frontendService')->setMetaTitle('404');
	}

	public function show403Action() {
		$this->tag->setTitle('Forbidden');
		$this->getDI()->get('frontendService')->setMetaTitle('403');
	}

	public function show500Action() {
		$this->tag->setTitle('Internal server error');
		$this->getDI()->get('frontendService')->setMetaTitle('500');
	}

}

