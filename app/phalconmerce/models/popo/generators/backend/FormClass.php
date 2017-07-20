<?php

namespace Phalconmerce\Models\Popo\Generators\Backend;

use Phalcon\Di;

class FormClass {
	/** @var string */
	protected $className;

	public function __construct($className) {
		$this->className = $className;
	}

	/**
	 * @return string
	 */
	public function getPhpContent() {
		$phpContent = <<<'EOT'
<?php

namespace Backend\Forms;

class ##CLASSNAME##Form extends FormBase {
	public function initialize($entity = null, $options = array()) {
		parent::initialize($entity, $options);

		$this->addElementsToForm();
	}
}
EOT;

		$phpContent = str_replace('##CLASSNAME##', $this->className, $phpContent);
		return $phpContent;
	}

	/**
	 * Get backend controller's directory
	 * @return string
	 */
	public static function getDirectory() {
		return APP_PATH.DIRECTORY_SEPARATOR.'backend'.DIRECTORY_SEPARATOR.'forms';
	}

	/**
	 * Get backend controller's class name
	 * @return string
	 */
	public function getClassName() {
		return $this->className.'Form';
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