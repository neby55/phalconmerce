<?php

namespace Phalconmerce\Plugins\Abstracts;

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;

/**
 * SecurityPlugin
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
abstract class AbstractBackendSecurityPlugin extends Plugin
{
	/**
	 * To modify roles' definitions, you should overload this method
	 * @return \Phalcon\Acl\Role[]
	 */
	protected function getRoles() {
		return array(
			'admin'  => new Role(
				'admin',
				'Administrators'
			),
			'user'  => new Role(
				'user',
				'Registered backend user'
			),
			'guest'  => new Role(
				'guest',
				'Any visitor'
			),
		);
	}

	/**
	 * To modify resources' definitions, you should overload this method
	 */
	protected static function getAllResources() {
		$resources['guest'] = array(
			'errors' => array('show404', 'show403', 'show500'),
			'index' => array('login')
		);
		$resources['user'] = array(
			'index' => array('index')
		);
		$resources['user'] = array_merge($resources['guest'], $resources['user']);
	}

	/**
	 * @param string $role
	 * @return bool
	 */
	protected function getResources($role) {
		$resourcesList = self::getAllResources();

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
	public function getAcl()
	{
		if (!isset($this->persistent->acl)) {

			$acl = new AclList();

			$acl->setDefaultAction(Acl::DENY);

			// settings up roles and its resources (Controllers & Actions)
			foreach ($this->getRoles() as $role) {
				$acl->addRole($role);
			}

			//Private area resources
			$privateResources = array(
				'companies'    => array('index', 'search', 'new', 'edit', 'save', 'create', 'delete'),
				'products'     => array('index', 'search', 'new', 'edit', 'save', 'create', 'delete'),
				'producttypes' => array('index', 'search', 'new', 'edit', 'save', 'create', 'delete'),
				'invoices'     => array('index', 'profile')
			);
			foreach ($privateResources as $resource => $actions) {
				$acl->addResource(new Resource($resource), $actions);
			}

			//Public area resources
			$publicResources = array(
				'index'      => array('index'),
				'about'      => array('index'),
				'register'   => array('index'),
				'errors'     => array('show401', 'show404', 'show500'),
				'session'    => array('index', 'register', 'start', 'end'),
				'contact'    => array('index', 'send')
			);
			foreach ($publicResources as $resource => $actions) {
				$acl->addResource(new Resource($resource), $actions);
			}

			//Grant access to public areas to both users and guests
			foreach ($roles as $role) {
				foreach ($publicResources as $resource => $actions) {
					foreach ($actions as $action){
						$acl->allow($role->getName(), $resource, $action);
					}
				}
			}

			//Grant access to private area to role Users
			foreach ($privateResources as $resource => $actions) {
				foreach ($actions as $action){
					$acl->allow('Users', $resource, $action);
				}
			}

			//The acl is stored in session, APC would be useful here too
			$this->persistent->acl = $acl;
		}

		return $this->persistent->acl;
	}

	/**
	 * This action is executed before execute any action in the application
	 *
	 * @param Event $event
	 * @param Dispatcher $dispatcher
	 * @return bool
	 */
	public function beforeDispatch(Event $event, Dispatcher $dispatcher)
	{

		$user = $this->session->get('user');
		if (is_object($user) && is_a($user, '\Phalconmerce\Models\Popo\BackendUser')){
			$role = $user->role;
		}
		else {
			$role = 'guest';
		}

		$controller = $dispatcher->getControllerName();
		$action = $dispatcher->getActionName();

		$acl = $this->getAcl();

		if (!$acl->isResource($controller)) {
			$dispatcher->forward([
				'controller' => 'errors',
				'action'     => 'show404'
			]);

			return false;
		}

		$allowed = $acl->isAllowed($role, $controller, $action);
		if (!$allowed) {
			$dispatcher->forward(array(
				'controller' => 'errors',
				'action'     => 'show401'
			));
                        $this->session->destroy();
			return false;
		}
	}
}
