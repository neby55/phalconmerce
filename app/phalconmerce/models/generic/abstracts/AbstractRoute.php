<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models\Generic\Abstracts;

abstract class AbstractRoute {
	/** @var string */
	protected $permalink;
	/** @var string */
	protected $controllerName;
	/** @var string */
	protected $actionName;
	/** @var string */
	protected $name;
	/** @var array */
	protected $customParameters;

	public function __construct($permalink, $controllerName, $actionName, $name='', $customParameters=array()) {
		$this->permalink = $permalink;
		$this->controllerName = $controllerName;
		$this->actionName = $actionName;
		$this->name = $name;
		$this->customParameters = $customParameters;
	}

	/**
	 * @return string
	 */
	public function getPermalink() {
		return $this->permalink;
	}

	/**
	 * @return string
	 */
	public function getControllerName() {
		return $this->controllerName;
	}

	/**
	 * @return string
	 */
	public function getActionName() {
		return $this->actionName;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function getCustomParameters() {
		return $this->customParameters;
	}

	/**
	 * @return array
	 */
	public function getAddRouteArray() {
		$routeArray = array(
			"controller" => $this->controllerName,
			"action" => $this->actionName
		);
		if (!empty($this->customParameters)) {
			foreach ($this->customParameters as $currentParameterName=>$customParameterValue) {
				$routeArray[$currentParameterName] = $customParameterValue;
			}
		}

		return $routeArray;
	}

	/**
	 * @param AbstractRoute[] $routesList
	 * @param \Phalcon\Mvc\Router $router
	 * @return \Phalcon\Mvc\Router
	 */
	public static function addRoutesToRouter($routesList, $router) {
		if (is_array($routesList)) {
			/**
			 * @var AbstractRoute $currentRoute
			 */
			foreach ($routesList as $currentRoute) {
				if (is_a($currentRoute, '\Phalconmerce\Models\Generic\Abstracts\AbstractRoute')) {
					//echo 'adding '.$currentRoute->getPermalink().'<br>';
					if ($currentRoute->getName() != "") {
						$router->add($currentRoute->getPermalink(),
							$currentRoute->getAddRouteArray()
						)->setName($currentRoute->getName());
					}
					else {
						$router->add($currentRoute->getPermalink(),
							$currentRoute->getAddRouteArray()
						);
					}
				}
			}
		}
		return $router;
	}
}