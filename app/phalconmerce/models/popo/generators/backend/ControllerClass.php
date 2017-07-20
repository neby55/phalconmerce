<?php

namespace Phalconmerce\Models\Popo\Generators\Backend;

use Phalcon\Di;

class ControllerClass {
	/** @var string */
	protected $className;
	/** @var string */
	protected $controllerName;

	public function __construct($className, $controllerName='') {
		$this->className = $className;

		if (!empty($controllerName)) {
			$this->controllerName = $controllerName;
		}
		else {
			$this->controllerName = strtolower($this->className);
		}
	}

	/**
	 * @return string
	 */
	public function getPhpContent() {
		$phpContent = <<<'EOT'
<?php

namespace Backend\Controllers;

use Backend\Controllers\ControllerBase;
use Backend\Forms\##CLASSNAME##Form;
use Phalconmerce\Models\Popo\##CLASSNAME##;
use Phalconmerce\Models\Utils;

class ##CLASSNAME##Controller extends ControllerBase {
	public function initialize() {
		parent::initialize();
		$this->tag->setTitle('Set ##CLASSNAME##');
		$this->setSubtitle('##CLASSNAME##');
	}

	/**
	 * Shows the index action
	 */
	public function indexAction() {
		parent::indexAction();
		// Get all currencies
		$list = ##CLASSNAME##::find();
		//Utils::debug($list);exit;
		$this->view->setVar('list', $list);

		// Set columns displayed (key=propertyName, value=label)
		$this->view->setVar('listActionProperties', array(
			// 'myProperty' => 'Label',
		));
	}

	/**
	 * Shows the form to create a new object
	 */
	public function newAction() {
		if (!$this->request->isPost()) {
			$this->view->form = new ##CLASSNAME##Form();
		}
	}

	/**
	 * Edits an object based on its id
	 * @param mixed $id
	 * @return bool
	 */
	public function editAction($id) {

		if (!$this->request->isPost()) {
			// Get all objects
			$object = ##CLASSNAME##::findFirstById($id);
			if (!$object) {
				$this->flash->error("##CLASSNAME## was not found");

				$this->dispatcher->forward(
					[
						"controller" => "##CONTROLLER_NAME##",
						"action" => "index",
					]
				);
				return false;
			}

			$this->view->form = new ##CLASSNAME##Form($object, array('edit' => true));
		}
	}

	/**
	 * Saves current object in screen
	 */
	public function saveAction() {
		if (!$this->request->isPost()) {
			$this->dispatcher->forward(
				[
					"controller" => "##CONTROLLER_NAME##",
					"action" => "index",
				]
			);
			return false;
		}

		$id = $this->request->getPost("id", "int");

		// If creation
		if (empty($id)) {
			$object = new ##CLASSNAME##();
		}
		else {
			$object = ##CLASSNAME##::findFirstById($id);
		}

		if (!$object) {
			$this->flash->error("##CLASSNAME## does not exist");

			$this->dispatcher->forward(
				[
					"controller" => "##CONTROLLER_NAME##",
					"action" => "index",
				]
			);
			return false;
		}

		$form = new ##CLASSNAME##Form;

		$data = $this->request->getPost();
		if (!$form->isValid($data, $object)) {
			foreach ($form->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => "##CONTROLLER_NAME##",
					"action" => "new",
				]
			);
			return false;
		}

		if ($object->save() == false) {
			foreach ($object->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => "##CONTROLLER_NAME##",
					"action" => "new",
				]
			);
			return false;
		}

		$form->clear();

		$this->flash->success("##CLASSNAME## was updated successfully");

		$this->response->redirect('##CONTROLLER_NAME##/edit/'.$object->id);
		return true;
	}

	/**
	 * Deletes an object
	 *
	 * @param int $id
	 * @return bool
	 */
	public function deleteAction($id) {

		$object = ##CLASSNAME##::findFirstById($id);
		if (!$object) {
			$this->flash->error("##CLASSNAME## was not found");

			$this->dispatcher->forward(
				[
					"controller" => "##CONTROLLER_NAME##",
					"action" => "index",
				]
			);
			return false;
		}

		if (!$object->delete()) {
			foreach ($object->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(
				[
					"controller" => "##CONTROLLER_NAME##",
					"action" => "search",
				]
			);
			return false;
		}

		$this->flashSession->success("##CLASSNAME## was deleted");

		$this->response->redirect('##CONTROLLER_NAME##/');
		return true;
	}
}
EOT;

		$phpContent = str_replace(array('##CLASSNAME##', '##CONTROLLER_NAME##'), array($this->className, strtolower($this->className)), $phpContent);
		return $phpContent;
	}

	/**
	 * Get backend controller's directory
	 * @return string
	 */
	public static function getDirectory() {
		return APP_PATH.DIRECTORY_SEPARATOR.'backend'.DIRECTORY_SEPARATOR.'controllers';
	}

	/**
	 * Get backend controller's class name
	 * @return string
	 */
	public function getClassName() {
		return $this->className.'Controller';
	}

	/**
	 * @param string $content
	 * @return int
	 */
	public function save($content) {
		$currentNewClassFilename = self::getDirectory().DIRECTORY_SEPARATOR.$this->getClassName().'.php';
		return file_put_contents($currentNewClassFilename, $content) !== false;
	}
}