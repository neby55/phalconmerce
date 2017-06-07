<?php

namespace Backend\Controllers;

class IndexController extends ControllerBase {

    public function indexAction() {
	    echo 'backend';

	    $toto = array('test utils', 'phalconmerce');
	    \Phalconmerce\Models\Utils::debug($toto);
	    exit;
    }

}

