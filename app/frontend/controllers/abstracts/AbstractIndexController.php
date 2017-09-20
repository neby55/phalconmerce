<?php

namespace Frontend\Controllers\Abstracts;

use Frontend\Controllers\ControllerBase;
use Phalconmerce\Models\Utils;

abstract class AbstractIndexController extends ControllerBase {

	public function indexAction() {
		$this->setSubtitle('Dashboard');
		$this->tag->setTitle('Home');
	}
}

