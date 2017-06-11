<?php

namespace Phalconmerce\Models;

class AbstractModelManyToMany extends AbstractModel {
	public function initialize() {
		// Checking prefix value before setting new value automatically
		if (empty($this->prefix)) {
			// TODO check if __CLASS__ refers to parent or called class
			$this->setPrefix(strtolower(preg_replace('/([^A-Z])*/', '', __CLASS__)).'_');
		}
	}
}