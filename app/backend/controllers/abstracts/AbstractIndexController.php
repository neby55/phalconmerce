<?php

namespace Backend\Controllers\Abstracts;

use Backend\Controllers\ControllerBase;
use Phalconmerce\Models\Utils;

abstract class AbstractIndexController extends ControllerBase {

	public function indexAction() {
		$this->setSubtitle('Dashboard');
		$this->tag->setTitle('Home');
	}

	public function loginAction() {
		if ($this->request->isPost()) {
			$config = $this->getDI()->get('config');

			$email = $this->request->getPost($config->adminDir.'-email', 'email', '', true);
			$password = $this->request->getPost($config->adminDir.'-password', null, '', true);
			Utils::debug($email);
			Utils::debug($password);


		}
		$this->tag->setTitle('Login');
		$this->view->setVars(array(
			'test' => 'toto',
			'titi' => 'tata'
		));
	}

}

