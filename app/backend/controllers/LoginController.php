<?php

namespace Backend\Controllers;

use Phalconmerce\Models\Popo\BackendUser;
use Phalconmerce\Models\Utils;

class LoginController extends ControllerBase {

	public function indexAction() {
		if ($this->request->isPost()) {
			$config = $this->getDI()->get('config');

			$email = $this->request->getPost($config->adminDir . '-email', 'email', '', true);
			$password = $this->request->getPost($config->adminDir . '-password', null, '', true);

			Utils::debug($email);
			$backendUser = BackendUser::findByEmail($email);

			if ($backendUser) {
				if ($this->security->checkHash($password, $backendUser->hashedPassword)) {
					// TODO log user in session
					die('a');
				}
			} else {
				// To protect against timing attacks. Regardless of whether a user exists or not, the script will take roughly the same amount as it will always be computing a hash.
				$this->security->hash(rand());
			}
			die('c');
		}
		$this->tag->setTitle('Login');
		$this->view->setVars(array(
			'test' => 'toto',
			'titi' => 'tata'
		));
		$this->view->setTemplateBefore('main_default');
	}

}

