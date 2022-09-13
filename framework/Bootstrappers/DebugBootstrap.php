<?php
namespace Repository\Component\Bootstrappers;

use Repository\Component\Debug\ExceptionServiceProvider;

/**
 * Application Debug Bar Bootstrapper.
 *
 * @package	  \Repository\Component\Bootstrap
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class DebugBootstrap extends Bootstrap
{	
	/**
	 * @param \Repository\Component\Foundation\Application $app
	 */	
	public function bootstrap()
	{
		$this->app->register(new ExceptionServiceProvider($this->app));
		$this->app['error']->register();
		$this->app['exception']->register();
	}
}