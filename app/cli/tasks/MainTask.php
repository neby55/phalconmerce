<?php

namespace Cli\Tasks;

use Cli\Models\Task;

class MainTask extends Task {
	public function mainAction() {
		echo PHP_EOL;
		echo "You have 3 tasks available helping you to construct your website." . PHP_EOL;
		echo PHP_EOL;
		echo 'Usage :'.self::TAB_CHARACTER. 'php app/cli.php popo'.PHP_EOL;
		echo self::TAB_CHARACTER. 'php app/cli.php table' . PHP_EOL;
		echo self::TAB_CHARACTER. 'php app/cli.php backend'.PHP_EOL;
		echo PHP_EOL;
	}
}