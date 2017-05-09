<?php
/**
 * Created by PhpStorm.
 * User: proGweb
 * Date: 09/05/2017
 * Time: 11:29
 */

namespace Phalconmerce\Cli;

class Console extends \Phalcon\Cli\Console {
	/**
	 * Handle the whole command-line tasks
	 *
	 * @param array $arguments
	 * @param string $shortOpts
	 * @param array $longOpts
	 */
	public function handle($arguments = array(), $shortOpts='', $longOpts=array()) {
		$this->_options = getopt($shortOpts, $longOpts);

		parent::handle($arguments);
	}

	/**
	 * @return mixed
	 */
	public function getOptions() {
		return $this->_options;
	}
}