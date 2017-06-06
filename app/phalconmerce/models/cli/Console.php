<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models\Cli;

abstract class Console extends \Phalcon\Cli\Console {
	public static $shortOpts = '';

	public static $longOpts  = array(
		"table-prefix:",
		"all",
		"delete",
	);

	/**
	 * Handle the whole command-line tasks
	 */
	public function handle() {
		global $argv;

		/**
		 * Process the console arguments
		 */
		$arguments = [];
		$counter = 0;
		foreach ($argv as $arg) {
			if (substr($arg,0,1) != '-') {
				if ($counter === 1) {
					$arguments["task"] = $arg;
				}
				elseif ($counter === 2) {
					$arguments["action"] = $arg;
				}
				elseif ($counter >= 3) {
					$arguments["params"][] = $arg;
				}
				$counter++;
			}
		}
		$this->_options = getopt(self::$shortOpts, self::$longOpts);

		parent::handle($arguments);
	}

	/**
	 * @return mixed
	 */
	public function getOptions() {
		return $this->_options;
	}
}