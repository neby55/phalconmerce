<?php

namespace Phalconmerce\Models\Popo\Generators\Backend;

use Phalcon\Di;

class ViewsPhtml {
	/** @var string */
	protected $className;
	/** @var string */
	protected $controllerName;

	public function __construct($className, $controllerName = '') {
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
	public function getNewContent() {
		return $this->getEditContent();
	}

	/**
	 * @return string
	 */
	public function getEditContent() {
		$content = <<<'EOT'
<?php

require dirname(__DIR__).DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'edit.phtml';
EOT;

		return $content;
	}

	/**
	 * @return string
	 */
	public function getIndexContent() {
		$content = <<<'EOT'
<?php

require dirname(__DIR__).DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'index.phtml';
EOT;

		return $content;
	}

	/**
	 * Get backend controller's directory
	 * @return string
	 */
	public static function getDirectory() {
		return APP_PATH . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . Di::getDefault()->get('config')->adminTheme;
	}

	/**
	 * Get backend controller's class name
	 * @return string
	 */
	public function getNewFileName() {
		return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->controllerName . DIRECTORY_SEPARATOR . 'new.phtml';
	}

	/**
	 * Get backend controller's class name
	 * @return string
	 */
	public function getEditFileName() {
		return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->controllerName . DIRECTORY_SEPARATOR . 'edit.phtml';
	}

	/**
	 * Get backend controller's class name
	 * @return string
	 */
	public function getIndexFileName() {
		return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->controllerName . DIRECTORY_SEPARATOR . 'index.phtml';
	}

	/**
	 * @return int
	 */
	public function save() {
		$bReturn = true;
		// Directory check
		if (!file_exists(self::getDirectory().DIRECTORY_SEPARATOR.$this->controllerName)) {
			mkdir(self::getDirectory().DIRECTORY_SEPARATOR.$this->controllerName);
		}
		// Edit
		$bReturn = $bReturn && file_put_contents($this->getEditFileName(), $this->getEditContent()) !== false;
		// Index
		$bReturn = $bReturn && file_put_contents($this->getIndexFileName(), $this->getIndexContent()) !== false;
		// New
		$bReturn = $bReturn && file_put_contents($this->getNewFileName(), $this->getNewContent()) !== false;

		return $bReturn;
	}
}