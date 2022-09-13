<?php
namespace Repository\Component\Log;

use Repository\Component\Support\ServiceProvider;

/**
 * Logger Service Provider.
 *
 * @package	  \Repository\Component\Log
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class LoggerServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind('logger', function($app) {
			$logger = new Manager($app);
			
			return $logger->getInstance();
		});
	}
}