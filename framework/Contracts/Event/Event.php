<?php
namespace Repository\Component\Contracts\Event;

/**
 * Event Interface.
 * 
 * @package	 \Repository\Component\Contracts\Event
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface Event
{
	/**
	 * Define your event name
	 * @return string
	 */
	public function getName();
}