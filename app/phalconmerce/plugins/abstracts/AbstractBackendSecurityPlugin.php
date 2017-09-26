<?php

namespace Phalconmerce\Plugins\Abstracts;

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalconmerce\Models\Utils;

/**
 * SecurityPlugin
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
abstract class AbstractBackendSecurityPlugin extends Plugin {
	/**
	 * To modify roles' definitions, you should overload this method
	 * @return \Phalcon\Acl\Role[]
	 */
	protected function getRoles() {
		return array(
			'admin' => new Role(
				'admin',
				'Administrators'
			),
			'user' => new Role(
				'user',
				'Registered backend user'
			),
			'guest' => new Role(
				'guest',
				'Any visitor'
			),
		);
	}

	/**
	 * To modify resources' definitions, you should overload this method
	 * @return array
	 */
	protected function getAllResources() {
		$resources = array();
		// Guest
		$resources['guest'] = array(
			'errors' => array('show404', 'show403', 'show500'),
			'login' => array('index')
		);

		// User
		$resources['user'] = array(
			'index' => array('index'),
			'login' => array('logout')
		);
		$resources['user'] = array_merge_recursive($resources['guest'], $resources['user']);

		return $resources;
	}

	/**
	 * @param string $role
	 * @return array|bool
	 */
	protected function getResources($role) {
		$resourcesList = $this->getAllResources();

		if (array_key_exists($role, $resourcesList)) {
			return $resourcesList[$role];
		}
		return false;
	}

	/**
	 * Returns an existing or new access control list
	 *
	 * @returns AclList
	 */
	public function getAcl() {
		// TODO uncomment this if for production
		//if (!isset($this->persistent->acl)) {

			$acl = new AclList();

			$acl->setDefaultAction(Acl::DENY);

			// settings up roles and its resources (Controllers & Actions)
			foreach ($this->getRoles() as $role) {
				$acl->addRole($role);

				$currentResources = $this->getResources($role->getName());
				// Admins have no resources defined, so we have to check the $variable to avoid a warning
				if (!empty($currentResources)) {
					foreach ($currentResources as $resource => $actions) {
						$acl->addResource(new Resource($resource), $actions);
						// To avoid warning if resources array's structure is wrong
						if (!is_array($actions) && is_string($actions)) {
							$actions = array($actions);
						}
						foreach ($actions as $action) {
							$acl->allow($role->getName(), $resource, $action);
						}
					}
				}
			}

			//The acl is stored in session, APC would be useful here too
			$this->persistent->acl = $acl;
		/*}*/

		return $this->persistent->acl;
	}

	/**
	 * This action is executed before execute any action in the application
	 *
	 * @param Event $event
	 * @param Dispatcher $dispatcher
	 * @return bool
	 */
	public function beforeDispatch(Event $event, Dispatcher $dispatcher) {
		$user = $this->di->get('backendService')->getConnectedUser();
		if (is_object($user) && is_a($user, '\Phalconmerce\Models\Popo\Abstracts\AbstractBackendUser')) {
			$role = $user->role;
		}
		else {
			$role = '';
		}

		// Admin can access to every page, so we check the others
		if ($role != 'admin') {
			$controller = $dispatcher->getControllerName();
			$action = $dispatcher->getActionName();

			$acl = $this->getAcl();
			if (!$acl->isResource($controller)) {
				$dispatcher->forward([
					'controller' => 'errors',
					'action' => 'show403'
				]);

				return false;
			}

			$allowed = $acl->isAllowed($role, $controller, $action);
			if (!$allowed) {
				// If connected
				if (!empty($role)) {
					$this->flash('Can\'t access to '.$controller.'::'.$action);
					$dispatcher->forward(array(
						'controller' => 'errors',
						'action' => 'show403'
					));
					return false;
				}
				// If not, redirect to signin
				else {
					// If login page
					if ($controller == 'errors' || ($controller == 'login' && $action == 'index')) {
						return true;
					}
					else {
						$this->view->disable();
						return $this->response->redirect(array(
							'for' => 'backend-login'
						));
					}
				}
			}
		}

		return true;
	}
}
