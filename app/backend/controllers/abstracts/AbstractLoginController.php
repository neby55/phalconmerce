<?php

namespace Backend\Controllers\Abstracts;

use Backend\Controllers\ControllerBase;
use Phalconmerce\Models\Popo\BackendUser;
use Phalconmerce\Models\Utils;
use Phalconmerce\Services\BackendService;

abstract class AbstractLoginController extends AbstractControllerBase {

	/**
	 * @return bool|\Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
	 */
	public function indexAction() {
		if ($this->request->isPost()) {
			// Token sent in login form is checked here, so never remove the input hidden
			if ($this->security->checkToken()) {
				$config = $this->getDI()->get('config');

				$email = $this->request->getPost($config->adminDir . '-email', 'email', '', true);
				$password = $this->request->getPost($config->adminDir . '-password', null, '', true);

				/** @var \Phalconmerce\Models\Popo\Abstracts\AbstractBackendUser $backendUser */
				$backendUser = BackendUser::findByEmail($email);

				if ($backendUser && $backendUser->status == 1) {
					if ($this->security->checkHash($password, $backendUser->hashedPassword)) {
						if ($this->di->get('backendService')->setConnectedUser($backendUser)) {
							$this->flashSession->success($this->translate('Connected'));
							return $this->redirectToRoute('backend-index');
						}
						else {
							$this->flashSession->error($this->translate('User connection failed'));
							return $this->redirectToRoute('backend-login');
						}
					}
					else {
						$this->flashSession->error($this->translate('Email/Password not recognized'));
						return $this->redirectToRoute('backend-login');
					}
				}
				else {
					// To protect against timing attacks. Regardless of whether a user exists or not, the script will take roughly the same amount as it will always be computing a hash.
					$this->security->hash(rand());
					$this->flashSession->error($this->translate('Email/Password not recognized'));
					return $this->redirectToRoute('backend-login');
				}
			}
			else {
				$this->flashSession->notice('Disconnected (CSRF protection)s');
				return $this->redirectToRoute('backend-login');
			}
		}
		// GET
		else {
			$this->tag->setTitle($this->translate('Login'));
			$this->view->setTemplateBefore('main_default');
		}
	}

	/**
	 * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
	 */
	public function logoutAction() {
		$this->di->get('backendService')->disconnectUser();
		$this->flashSession->success($this->translate('Logged out'));
		return $this->redirectToRoute('backend-login');
	}
}

