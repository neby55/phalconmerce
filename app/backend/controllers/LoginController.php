<?php

namespace Backend\Controllers;

use Phalconmerce\Models\Popo\BackendUser;
use Phalconmerce\Models\Utils;

class LoginController extends ControllerBase {

	public function indexAction() {
		if ($this->request->isPost()) {
			// Token sent in login form is checked here, so never remove the input hidden
			if ($this->security->checkToken()) {
				$config = $this->getDI()->get('config');

				$email = $this->request->getPost($config->adminDir . '-email', 'email', '', true);
				$password = $this->request->getPost($config->adminDir . '-password', null, '', true);

				Utils::debug($email);
				$backendUser = BackendUser::findByEmail($email);

				if ($backendUser) {
					if ($this->security->checkHash($password, $backendUser->hashedPassword)) {
						$this->session->set('user', $backendUser);
						// TODO use translation system
						$this->flashSession->success('Connected');

						return $this->redirectToRoute('backend-index');
					}
					else {
						$this->flashSession->error('Email/Password not recognized');
						return $this->redirectToRoute('backend-login');
					}
				} else {
					// To protect against timing attacks. Regardless of whether a user exists or not, the script will take roughly the same amount as it will always be computing a hash.
					$this->security->hash(rand());
					$this->flashSession->error('Email/Password not recognized');
					return $this->redirectToRoute('backend-login');
				}
			}
			else {
				$this->flashSession->notice('CSRF protection');
				return $this->redirectToRoute('backend-login');
			}
		}
		// GET
		else {
			$this->tag->setTitle('Login');
			$this->view->setVars(array(
				'test' => 'toto',
				'titi' => 'tata'
			));
			$this->view->setTemplateBefore('main_default');
		}
	}

}

