<?php

namespace Backend\Controllers;

use Backend\Forms\Labels;
use Phalcon\Mvc\Controller;
use Phalconmerce\Models\Utils;

class ControllerBase extends Controller {
	/**
	 * @var string
	 */
	protected $popoClassName;

	public function initialize() {
		//$this->popoClassName = str_replace('Form', '',(new \ReflectionClass($this))->getShortName());
		$tmp = explode('\\', static::class);
		$this->popoClassName = end($tmp);

		if (substr($this->popoClassName, -10) == 'Controller') {
			$this->popoClassName = substr($this->popoClassName, 0, -10);
		}

		$config = $this->getDI()->get('config');
		$this->view->setVar('config', $config);

		$this->tag->prependTitle($config->adminTitle.' | ');
		$this->setSubtitle('Page Name');

		// Informations need in views (URL)
		$this->view->setVar('controllerURL', strtolower($this->popoClassName));
		$this->view->setVar('popoClassName', $this->popoClassName);

		$this->view->setTemplateBefore('main_default');

		// TODO Check user connection
		/** @var \Phalcon\Session\Adapter\Files $session */
		$session = $this->di->getShared('session');
		$user = $session->get('connectedUser', false);
		$this->view->setVar('user', $user);
		$this->view->setVar('url', $this->di->getShared('url'));
		/*
				if ($user === false) {
					$this->view->setTemplateBefore('main_default');
					if ($this->dispatcher->getControllerName() != 'index' || $this->dispatcher->getActionName() != 'login') {
						$this->dispatcher->forward(
							[
								"controller" => "index",
								"action" => "login",
							]
						);
					}
				}
				else {
					$this->view->setTemplateBefore('main_connected');
				}*/
		$this->view->setTemplateBefore('main_connected');
	}

	public function setSubtitle($str) {
		$this->view->setVar('h1', $str);
	}

	/**
	 * Send table headers for the index action
	 */
	public function indexAction() {
		// Provide labels Object handling labels for table headers
		$this->view->setVar('labelsObject', new Labels($this->popoClassName));
	}
}
