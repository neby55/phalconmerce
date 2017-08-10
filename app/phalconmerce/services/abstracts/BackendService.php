<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Services\Abstracts;


abstract class BackendService  implements \Phalcon\Di\InjectionAwareInterface {

	protected $_dependencyInjector;

	/**
	 * Sets the dependency injector
	 *
	 * @param mixed $dependencyInjector
	 */
	public function setDI(\Phalcon\DiInterface $dependencyInjector) {
		$this->_dependencyInjector = $dependencyInjector;
	}

	/**
	 * Returns the internal dependency injector
	 *
	 * @return \Phalcon\DiInterface
	 */
	public function getDI() {
		return $this->_dependencyInjector;
	}

}