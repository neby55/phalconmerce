<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Forms\Element;

class Time extends \Phalcon\Forms\Element\Date  {
	public function render($attributes = null) {
		$html = \Phalcon\Tag::timeField($this->prepareAttributes($attributes));
		return $html;
	}
}
