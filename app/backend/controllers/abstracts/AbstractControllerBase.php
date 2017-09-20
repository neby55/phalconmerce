<?php

namespace Backend\Controllers\Abstracts;

use Backend\Forms\Labels;
use Backend\Forms\UrlForm;
use Phalcon\Mvc\Controller;
use Phalconmerce\Models\Popo\Generators\Popo\PhpClass;
use Phalconmerce\Models\Popo\Lang;
use Phalconmerce\Models\Popo\Url;
use Phalconmerce\Models\Utils;

abstract class AbstractControllerBase extends Controller {

	protected static $entity = 'to override';

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

		// Disable browser caching
		$this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
		$this->response->setHeader('Pragma', 'no-cache');
		$this->response->setHeader('Expires', '0');

		// Informations need in views (URL)
		$this->view->setVar('controllerURL', strtolower($this->popoClassName));
		$this->view->setVar('popoClassName', $this->popoClassName);

		$this->view->setTemplateBefore('main_connected');

		// Disabling default validators requiring all fields to be filled
		\Phalcon\Mvc\Model::setup(array(
			'notNullValidations' => false
		));
	}

	public function setSubtitle($str) {
		$this->view->setVar('h1', $this->di->get('backendService')->t($str));
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
			if (array_key_exists('fragment', $params)) {
				return $this->response->redirect($this->url->get($params).'#'.$params['fragment']);
			}
			else {
				return $this->response->redirect($params);
			}
		}
		else {
			return $this->response->redirect(array(
				'for' => $routeName
			));
		}
	}

	/**
	 * @param string $str
	 * @return string
	 */
	public function translate($str) {
		return $this->di->get('backendService')->t($str);
	}

	public function addUrlForm($entityId) {
		if (!isset($this->view->formUrl) && !is_object($this->view->formUrl)) {
			$urlFormsList = array();

			$langList = Lang::find(array(
				'status' => 1
			));
			/** @var Lang $currentLang */
			foreach ($langList as $currentLang) {
				$urlObject = Url::findFirst(array(
					'entity = "'.self::$entity.'" AND entityId = :entity_id: AND fk_lang_id = :fk_lang_id:',
					'bind' => array(
						'entity_id' => $entityId,
						'fk_lang_id' => $currentLang->id
					)
				));

				// If creation
				if (empty($urlObject)) {
					$urlObject = new Url();
					$urlObject->entity = self::$entity;
					$urlObject->entityId = $entityId;
					$urlObject->fk_lang_id = $currentLang->id;
					$urlObject->status = 1;
				}

				$urlFormsList[$currentLang->code] = new UrlForm($urlObject);
			}
			$this->view->formUrl = $urlFormsList;
		}
	}

	/**
	 * Saves current object in screen
	 */
	public function saveUrlAction() {
		if (!$this->request->isPost()) {
			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "index",
				]
			);
			return false;
		}

		$id = $this->request->getPost("entity_id", "int");
		$langCode = $this->request->getPost("code", "string");
		$classname = PhpClass::POPO_NAMESPACE.'\\'.$this->popoClassName;

		$object = $classname::findFirstById($id);

		if (!$object) {
			$this->flash->error($this->popoClassName." does not exist");

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($id)
				]
			);
			return false;
		}

		// Get lang from code
		/** @var Lang $langObject */
		$langObject = Lang::findFirst(array(
			'code = :code:',
			'bind' => array(
				'code' => $langCode
			)
		));
		if (!$langObject) {
			$this->flash->error("Lang does not exist");

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($id)
				]
			);
			return false;
		}

		// Get URL from lang and id
		/** @var Url $urlObject */
		$urlObject = Url::findFirst(array(
			'entity = "'.self::$entity.'" AND entityId = :entity_id: AND fk_lang_id = :fk_lang_id:',
			'bind' => array(
				'entity_id' => $id,
				'fk_lang_id' => $langObject->id
			)
		));
		// If creation
		if (!$urlObject) {
			$urlObject = new Url();
			$urlObject->entity = self::$entity;
			$urlObject->entityId = $id;
			$urlObject->fk_lang_id = $langObject->id;
			$urlObject->status = 1;
		}

		$form = new UrlForm;

		$data = $this->request->getPost();
		if (!$form->isValid($data, $urlObject)) {
			$this->view->formUrl = array($langCode => $form);
			foreach ($form->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($id)
				]
			);
			return false;
		}

		if ($urlObject->save() == false) {
			$this->view->formUrl = array($langCode => $form);
			foreach ($urlObject->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => $this->dispatcher->getControllerName(),
					"action" => "edit",
					'params' => array($id)
				]
			);
			return false;
		}
		else {
			// Update permalinks cache file
			$this->updateUrlCache();
		}

		$form->clear();

		$this->view->formUrl = array($langCode => $form);

		$this->flash->success($this->popoClassName." successfully updated");

		return $this->redirectToRoute('backend-controller-edit', array('id' => $object->id, 'controller' => $this->dispatcher->getControllerName(), 'fragment'=>'tab-3'));
	}

	public function updateUrlCache() {
		$allUrl = Url::find('status = 1');

		$data = array();

		foreach ($allUrl as $currentUrlObject) {
			$data[$currentUrlObject->permalink] = $currentUrlObject;
		}

		return Utils::saveData($data, 'routes');
	}
}
