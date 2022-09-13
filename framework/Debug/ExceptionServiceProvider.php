<?php
namespace Repository\Component\Debug;

use Repository\Component\Support\ServiceProvider;

/**
 * Exception Service Provider.
 *
 * @package	  \Repository\Component\Debug
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ExceptionServiceProvider extends ServiceProvider
{
	/**
	 * @{inheritdoc}
	 * See \Repository\Component\Support\ServiceProvider::register()
	 */
	public function register()
	{
		$this->app->singleton('debug.config', function ($app) {
			return new \Repository\Component\Debug\Config($app);
		});
		
		$this->app->singleton('exception', function ($app) {
			$renderer = new ExceptionRenderer($app, $app['fs'], $app['request'], $app['response']);
			return new ExceptionHandler($app['debug.config'], $renderer);
		});
		
		$this->app->singleton('error', function ($app) {
			return new ErrorHandler($app['exception'], $app['logger']);
		});
	}
}