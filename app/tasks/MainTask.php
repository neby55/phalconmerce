<?php

use Phalconmerce\Cli\Task;

class MainTask extends Task {
	public function mainAction() {
		echo PHP_EOL;
		echo "You have only one task available for now : \"popo\"" . PHP_EOL;
		echo "      php app/cli.php popo" . PHP_EOL;
	}
}