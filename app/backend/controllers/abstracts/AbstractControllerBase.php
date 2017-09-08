<?php

namespace Backend\Controllers\Abstracts;

use Backend\Forms\Labels;
use Phalcon\Mvc\Controller;
use Phalconmerce\Models\Utils;

abstract class AbstractControllerBase extends Controller {
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

	/**
	 * @param string $routeName
	 * @param array $params
	 * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
	 */
	public function redirectToRoute($routeName, $params=array()) {
		$this->view->disable();
		if (is_array($params) && sizeof($params) > 0) {
			$params['for'] = $routeName;
			return $this->response->redirect($params);
		}
		else {
			return $this->response->redirect(array(
				'for' => $routeName
			));
		}
	}
}
