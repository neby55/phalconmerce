<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

$dir = dirname(__DIR__).DIRECTORY_SEPARATOR.'listeners';
if (is_dir($dir)) {
	// First, load ListenerBase
	if (file_exists($dir.DIRECTORY_SEPARATOR.'ListenerBase.php')) {
		include $dir.DIRECTORY_SEPARATOR.'ListenerBase.php';
	}
	// then, load each listener
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if (substr($file,0,1) != '.' && substr($file, -4) == '.php' && $file != 'ListenerBase.php') {
				$className = substr($file, 0, -4);

				// include class
				include $dir.DIRECTORY_SEPARATOR.$file;

				// Check if class exists
				$classFQCN = '\Phalconmerce\Listeners\\'.$className;
				if (class_exists($classFQCN)) {
					call_user_func(array($classFQCN, 'enable'));
				}
			}
		}
		closedir($dh);
	}
}