<?php
namespace Repository\Component\Contracts\Event;

/**
 * Event Listener Interface.
 * 
 * @package	 \Repository\Component\Contracts\Event
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface Listener
{
	/**
	 * Handle the  appropriate logic of the given event
	 * 
	 * @param \Repository\Component\Contracts\Event\Event $event
	 * 
	 * @return mixed
	 */
	public function handle(Event $event, \Closure $next);
}