<?php

namespace Backend\Controllers;

class IndexController extends ControllerBase {

    public function indexAction() {
	    $this->tag->setTitle('Home');
	    $this->view->setVars(array(
		    'test' => 'toto',
		    'titi' => 'tata'
	    ));
    }

}

