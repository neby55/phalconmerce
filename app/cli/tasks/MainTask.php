<?php

namespace Cli\Tasks;

use Cli\Models\Task;

class MainTask extends Task {
	protected static $tasks = array(
		'popo',
		'table',
		'abstract',
		'backend',
	);

	public function mainAction() {
		echo PHP_EOL;
		echo "You have ".count(self::$tasks)." tasks available helping you to construct your website." . PHP_EOL;
		echo PHP_EOL;
		echo 'Usage :';
		foreach (self::$tasks as $currentTask) {
			echo self::TAB_CHARACTER. 'php app/cli.php ' .$currentTask . PHP_EOL;
		}
		echo PHP_EOL;
	}
}