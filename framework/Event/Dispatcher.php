<?php
namespace Repository\Component\Event;

use Repository\Component\Contracts\Event\Event;
use Repository\Component\Contracts\Event\Listener;
use Repository\Component\Http\Response;
use Repository\Component\Pipeline\Pipeline;
use Repository\Component\Pipeline\Exception\PipelineException;
use Repository\Component\Contracts\Container\ContainerInterface as IContainer;

/**
 * Event Dispatcher.
 *
 * @package	  \Repository\Component\Event
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Dispatcher
{
	/**
	 * 
	 * Listener events
	 * @var array $listeners 
	 * 
	 */
	private $listeners = array();

	/**
	 * 
	 * Bind current running event
	 * @var string|bool $currentEvent 
	 * 
	 */
	private $currentEvent = null;

	/**
	 * 
	 * Ioc Container
	 * @var \Repository\Component\Container\Container $app
	 * 
	 */
	private $app;

	/**
	 * 
	 * Bind Ioc Container
	 * @param \Repository\Component\Container\Container $app
	 * 
	 */
	public function __construct(IContainer $app)
	{
		$this->app = $app;
	}

	/**
	 * 
	 * Add listeer event by event name
	 * 
	 * @param mixed $eventNames
	 * @param mixed $listener
	 * 
	 * @return bool
	 * 
	 */
	public function listen($eventNames, $listener, $priority = 10)
	{
		if (is_array($eventNames)) {
			foreach ($eventNames as $event) {
				if (!$this->isListenerHasQueued($event, $listener, $priority)) {
					$this->listeners[$event][$priority][] = $listener;
				}
			}
		} else {
			if (!$this->isListenerHasQueued($eventNames, $listener, $priority)) {
				$this->listeners[$eventNames][$priority][] = $listener;
			}
		}
	}

	private function isListenerHasQueued(string $eventName, $listener, $priority)
	{
		if (isset($this->listeners[$eventName][$priority])) {
			$listeners = $this->listeners[$eventName][$priority];

			if (in_array($listener, $listeners)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * 
	 * Count listener by the given event name and priority
	 * 
	 * @param string $eventName
	 * @param int $priority
	 * 
	 * @return int
	 * 
	 */
	public function countListenerOnEvent($eventName)
	{
		return count($this->listeners[$eventName]);
	}

	/**
	 * 
	 * Determine if the event has listener
	 * 
	 * @param string $eventName
	 * 
	 * @return bool
	 * 
	 */
	public function hasListeners($eventName)
	{
		return isset($this->listeners[$eventName]);
	}


	/**
	 * 
	 * Remove listener by the given approriate event name and their priority
	 * 
	 * @param string $eventName
	 * @param mixed $priority
	 * 
	 * @return bool
	 * 
	 */
	public function removeListener($eventName, $priority = 10)
	{
		unset($this->listeners[$eventName][$priority]);

		if (!isset($this->listeners[$eventName][$priority])) {
			return true;
		}
	}

	/**
	 * 
	 * Determine if the particular listener has been called
	 * 
	 * @param string $eventName
	 * @param mixed $priority
	 * 
	 * @return bool
	 * 
	 */
	public function isListening($eventName, $priority = 10)
	{
		return (!isset($this->listeners[$eventName][$priority])) ? true : false;
	}

	/**
	 * 
	 * Determine if the particular listener has been called
	 * 
	 * @return mixed
	 * 
	 */
	public function getCurrentEvent()
	{
		return $this->currentEvent;
	}

	/**
	 * 
	 * Get listener event by event name
	 * 
	 * @param string $eventName
	 * 
	 * @return mixed
	 * 
	 */
	public function getListeners($eventName)
	{
		if (!$this->hasListeners($eventName)) {
			return [];
		}

		//Sort event listeners by their priority
		ksort($this->listeners[$eventName]);
		return $this->listeners[$eventName];
	}

	/**
	 * 
	 * Fire events
	 * 
	 * @param mixed $events
	 * @param array $payloads Primitive parameters
	 * 
	 * @return void
	 * 
	 */
	public function fire($events, $payloads = array())
	{
		if (is_array($events)) {
			return $this->fireEvents($events, $payloads);
		}
		
		return $this->fireEvent($events, $payloads);
	}

	/**
	 * 
	 * Fire multiple events
	 * 
	 * @param array $events
	 * @param array $payloads Primitive parameters
	 * 
	 * @return void
	 * 
	 */
	private function fireEvents(array $events, $payloads = array())
	{
		foreach ($events as $event) {
			$this->fireEvent($event, $payloads);
		}
	}
	
	/**
	 * 
	 * Fire event
	 * 
	 * @param mixed $event Domain event
	 * @param array $payloads Primitive parameters
	 * 
	 * @return mixed
	 * 
	 */
	private function fireEvent($event, $payloads = array())
	{
		$response = null;

		if (is_string($event)) {
			$event = $this->app->make($event, $payloads);
		}

		if (!$event instanceof Event) {
			throw new \Exception("Event must be instance of " . Event::class);
		}

		if (!$this->hasListeners($event->getName())) {
			return;
		}

		$this->currentEvent = $event->getName();
		$availableListeners = $this->getListeners($event->getName());

		foreach ($availableListeners as $priority => &$listeners) {
			foreach ($listeners as $key => &$listener) {
				if (is_string($listener)) {
					$listeners[$key] = $this->app->make($listener);
				} else if ($listener instanceof \Closure) {
					$listeners[$key] = $listener;
				} else {
					$ex = "Listener must be instance of \Closure or " . Listener::class;
					throw new \Exception($ex);
				}
			}
		}
		
		foreach ($availableListeners as $priority => &$listeners) {
			$response = $this->doFireEvents($listeners, $event);
		}

		$this->currentEvent = null;
		
		return $response ? $response : new Event;
	}

	/**
	 * @inheritdoc
	 */
	public function doFireEvents(array $listeners, $payload)
	{
		if (count($listeners) < 1) { return; }

		try {
			return (new Pipeline)
				->send($payload)
				->through($listeners, 'handle')
				->then(function ($output) {
					return $output;
				})
				->execute();
		} catch (PipelineException $ex) {
			throw new PipelineException('Failed to execute event listener through pipeline', 0, $ex);
		}
	}
}