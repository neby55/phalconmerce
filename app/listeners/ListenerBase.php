<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 *
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Listeners;

use Phalcon\Di;
use Phalcon\Events\Event;
use Phalconmerce\Models\Utils;
use Phalcon\Mvc\User\Plugin;

// extending Plugin permits to have all Di properties in this class' objects, like Controllers
class ListenerBase extends Plugin {
	// Class constant that should be override on children's declaration
	const component = '';

	final public function eventListenerExample(Event $event, $parameterSent) {
		// event should be
		echo "Here, eventListenerExample\n";
		Utils::debug($event);
		Utils::debug($parameterSent);
	}

	public static function enable() {
		if (!empty(static::component)) {
			$currentClassName = static::class;
			Di::getDefault()->get('eventsManager')->attach(
				static::component,
				new $currentClassName()
			);
		}
	}
}