<?php
namespace Repository\Component\Bootstrappers;

/**
 * HTTP Kernel Bootstrapper.
 *
 * @package	  \Repository\Component\Bootstrap
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class HttpKernelBootstrap extends Bootstrap
{
	/**
	 * @{inheritdoc}
	 * See \Repository\Component\Contracts\Bootstrap\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
		$this->app->singleton(\App\Http\Kernel::class, function($app) {
			return new \App\Http\Kernel($app, $app['exception']);
		});
	}
}