<?php

namespace Cli\Tasks;

use Cli\Models\Task;

class MainTask extends Task {
	public function mainAction() {
		echo PHP_EOL;
		echo "You have 2 tasks available for now : \"popo\" and \"table\"" . PHP_EOL;
		echo "\tphp app/cli.php popo" . PHP_EOL;
		echo "\tphp app/cli.php table" . PHP_EOL;
		echo PHP_EOL;
	}
}