<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

use Phalcon\Loader;

/**
 * Phalcomerce namespaces
 *
 * @var \Phalcon\Config $configPhalconmerce
 */

$loader = new Loader();
$loader->registerNamespaces($configPhalconmerce->get('namespaces')->toArray());
$loader->register();