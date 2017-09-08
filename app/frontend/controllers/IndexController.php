<?php

namespace Frontend\Controllers;

class IndexController extends ControllerBase {

    public function indexAction() {
	    echo 'frontend';

	    /*$address = \Phalconmerce\Popo\Address::findFirst();
	    var_dump($address);
	    \Phalconmerce\Utils::debug($address);
	    exit;*/
    }

	public function route404Action() {
		echo 'front 404 not found :/';
	}

}

