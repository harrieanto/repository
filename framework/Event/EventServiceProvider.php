<?php
namespace Repository\Component\Event;

use Repository\Component\Support\ServiceProvider;

/**
 * Event Service Provider.
 *
 * @package	  \Repository\Component\Event
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class EventServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->singleton('event', function($app) {
			return new Dispatcher($app);
		});
	}
}