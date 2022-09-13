<?php
namespace Repository\Component\Support;

/**
 * Service Provider.
 * 
 * @package	  \Repository\Component\Support
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
abstract class ServiceProvider
{
	/**
	 * The Application instance.
	 *
	 * @var \Repository\Component\Foundation\Application
	 */
	protected $app;

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;


	/**
	 * Create a new service provider instance.
	 *
	 * @param  \Reposiitory\Component\Foundation\Application $app
	 * 
	 * @return void
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {}
	
	public function booted() {}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	abstract public function register();

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

	/**
	 * Get the events that trigger this service provider to register.
	 *
	 * @return array
	 */
	public function when()
	{
		return array();
	}

	/**
	 * Determine if the provider is deferred.
	 *
	 * @return bool
	 */
	public function isDeferred()
	{
		return $this->defer;
	}
}