<?php
namespace Repository\Component\Filesystem;

use Psr\Http\Message\StreamInterface;
use Repository\Component\Support\ServiceProvider;
use Repository\Component\Contracts\Filesystem\FilesystemInterface;

/**
 * Filesystem Service Provider.
 *
 * @package	  \Repository\Component\Filesystem
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class FilesystemServiceProvider extends ServiceProvider
{
	/**
	 * 
	 * Register the service provider.
	 *
	 * @return void
	 * 
	 */
	public function register()
	{
		$this->app->singleton('fs',  Filesystem::class);

		$this->app->bind(FilesystemInterface::class, function ($app) {
			return $app['fs'];
		});

		$this->app->bind(StreamInterface::class, function ($app) {
			return $app['fs'];
		});

		$this->app->bind('extension',  function($app) {
			return new Extension($app);
		});
	}
}