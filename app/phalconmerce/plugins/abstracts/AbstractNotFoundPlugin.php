<?php

namespace Phalconmerce\Plugins\Abstracts;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Dispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;

/**
 * NotFoundPlugin
 *
 * Handles not-found controller/actions
 */
class AbstractNotFoundPlugin extends Plugin
{

	/**
	 * This action is executed before execute any action in the application
	 *
	 * @param Event $event
	 * @param MvcDispatcher $dispatcher
	 * @param \Exception $exception
	 * @return boolean
	 */
	public function beforeException(Event $event, MvcDispatcher $dispatcher, \Exception $exception)
	{
		error_log($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());

		if ($exception instanceof DispatcherException) {
			switch ($exception->getCode()) {
				case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
				case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
					$this->di->get('logger')->error('Exception : '.$exception->getMessage());
					$dispatcher->forward(
						[
							'controller' => 'errors',
							'action'     => 'show404'
						]
					);
					return false;
			}
		}
		else {
			$this->di->get('logger')->error('Exception : '.$exception->getMessage());
			$this->di->get('logger')->error($exception->getTraceAsString());
		}

		$dispatcher->forward(
			[
				'controller' => 'errors',
				'action'     => 'show500'
			]
		);

		return false;
	}
}
