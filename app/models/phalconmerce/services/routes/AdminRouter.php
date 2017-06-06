<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Services\Routes;

use Phalcon\Di;
use Phalcon\Mvc\Router;

class AdminRouter extends Router {
	private function prependAdminDirToPattern($pattern) {
		// If admin directory not included in pattern => prepend it
		if (strpos($pattern, $this->getDI()->get('configPhalconmerce')->adminDir) === false) {
			$pattern = $this->getDI()->get('configPhalconmerce')->adminDir.'/'.$pattern;
		}
		return $pattern;
	}
	public function add($pattern, $paths = null, $httpMethods = null, $position = Router::POSITION_LAST) {
		$pattern = $this->prependAdminDirToPattern($pattern);
		parent::add($pattern, $paths, $httpMethods, $position);
	}

	public function addGet($pattern, $paths = null, $position = Router::POSITION_LAST) {
		$pattern = $this->prependAdminDirToPattern($pattern);
		parent::addGet($pattern, $paths, $position);
	}

	public function addPost($pattern, $paths = null, $position = Router::POSITION_LAST) {
		$pattern = $this->prependAdminDirToPattern($pattern);
		parent::addPost($pattern, $paths, $position);
	}

	public function addPut($pattern, $paths = null, $position = Router::POSITION_LAST) {
		$pattern = $this->prependAdminDirToPattern($pattern);
		parent::addPut($pattern, $paths, $position);
	}

	public function addPatch($pattern, $paths = null, $position = Router::POSITION_LAST) {
		$pattern = $this->prependAdminDirToPattern($pattern);
		parent::addPatch($pattern, $paths, $position);
	}

	public function addDelete($pattern, $paths = null, $position = Router::POSITION_LAST) {
		$pattern = $this->prependAdminDirToPattern($pattern);
		parent::addDelete($pattern, $paths, $position);
	}

	public function addOptions($pattern, $paths = null, $position = Router::POSITION_LAST) {
		$pattern = $this->prependAdminDirToPattern($pattern);
		parent::addOptions($pattern, $paths, $position);
	}

	public function addHead($pattern, $paths = null, $position = Router::POSITION_LAST) {
		$pattern = $this->prependAdminDirToPattern($pattern);
		parent::addHead($pattern, $paths, $position);
	}


}