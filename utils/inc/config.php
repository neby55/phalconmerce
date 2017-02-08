<?php

if (sizeof($argv) <= 0) {
	die('You must use this scipt with CLI');
}
// Delete script name
unset($argv[0]);

define('PATH', dirname(dirname(dirname(__FILE__))));
define('POPO_DIRECTORY', PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'phalconmerce'.DIRECTORY_SEPARATOR.'popo');
define('TAB_CHARACTER', "\t");

require dirname(__FILE__).'/functions.php';
require dirname(__FILE__).'/PhpClass.php';
require dirname(__FILE__).'/Property.php';