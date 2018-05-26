<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Models;

interface FilterInterface {
	/**
	 * @param int $id
	 * @return \Phalconmerce\Models\FilterData[]
	 */
	public static function getFilterDataList($id=0);
}