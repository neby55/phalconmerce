<?php

namespace Backend\Controllers;

use Backend\Forms\Labels;
use Phalcon\Mvc\Controller;

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

		$this->tag->prependTitle($this->getDI()->get('config')->adminTitle.' | ');
		$this->setSubtitle('Page Name');

		// Informations need in views (URL)
		$this->view->setVar('controllerURL', strtolower($this->popoClassName));
		$this->view->setVar('popoClassName', $this->popoClassName);

		$this->view->setTemplateBefore('main_default');

		// TODO Check user connection
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
