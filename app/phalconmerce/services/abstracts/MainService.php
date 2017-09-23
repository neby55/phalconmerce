<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Services\Abstracts;

use Phalconmerce\Models\Design;

abstract class MainService implements \Phalcon\Di\InjectionAwareInterface {

	protected $_dependencyInjector;

	/** @var Design[] */
	protected $designsList;

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

	public function loadDesignsIfNeeded() {
		if (!isset($this->designsList) || !is_array($this->designsList)) {
			$this->designsList = Design::loadAllDesigns();;
		}
	}

	/**
	 * @return string[]
	 */
	public function getDesignsSelectOptions() {
		$this->loadDesignsIfNeeded();

		$options = array('' => $this->getDi()->get('backendService')->t('choose'));

		if (is_array($this->designsList) && sizeof($this->designsList)) {
			foreach ($this->designsList as $currentDesign) {
				$options[$currentDesign->getSlug()] = $currentDesign->getName();
			}
		}

		return $options;
	}

	/**
	 * @return Design
	 */
	public function getDesign($slug) {
		$this->loadDesignsIfNeeded();

		if (is_array($this->designsList) && sizeof($this->designsList) && array_key_exists($slug, $this->designsList)) {
			return $this->designsList[$slug];
		}
	}

}