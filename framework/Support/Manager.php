<?php
namespace Repository\Component\Support;

use Closure;
use InvalidArgumentException;

/**
 * Manager - Easily manage your class to match with selected driver option.
 * 
 * @package	  \Repository\Component\Support
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
abstract class Manager
{
	/**
	 * 
	 * The application instance.
	 *
	 * @var \Repository\Component\Foundation\Application
	 * 
	 */
	protected $app;

	/**
	 * 
	 * The registered custom driver creators.
	 *
	 * @var array
	 * 
	 */
	protected $customCreators = [];

	/**
	 * 
	 * The array of created "drivers".
	 *
	 * @var array
	 * 
	 */
	protected $drivers = [];

	/**
	 * 
	 * Create a new manager instance.
	 *
	 * @param  \Repository\Component\Foundation\Application  $app
	 * 
	 * @return void
	 * 
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}

	/**
	 * 
	 * Get the default driver name.
	 *
	 * @return string
	 * 
	 */
	abstract public function getDefaultDriver();

	/**
	 * 
	 * Get a driver instance.
	 *
	 * @param  string  $driver
	 * 
	 * @return mixed
	 * 
	 */
	public function driver($driver = null)
	{
		$driver = $driver ?: $this->getDefaultDriver();

		// If the given driver has not been created before, we will create the instances
		// here and cache it so we can return it next time very quickly. If there is
		// already a driver created by this name, we'll just return that instance.
		if (! isset($this->drivers[$driver])) {
			$this->drivers[$driver] = $this->createDriver($driver);
		}

		return $this->drivers[$driver];
	}

	/**
	 * 
	 * Create a new driver instance.
	 *
	 * @param  string  $driver
	 * @return mixed
	 *
	 * @throws \InvalidArgumentException
	 * 
	 */
	protected function createDriver($driver)
	{
		// We'll check to see if a creator method exists for the given driver. If not we
		// will check for a custom driver creator, which allows developers to create
		// drivers using their own customized driver creator Closure to create it.
		if (isset($this->customCreators[$driver])) {
			return $this->callCustomCreator($driver);
		} else {
			$method = 'create'.Str::studly($driver).'Driver';

			if (method_exists($this, $method)) {
				return $this->$method();
			}
		}
		throw new InvalidArgumentException("Driver [$driver] not supported.");
	}

	/**
	 * 
	 * Call a custom driver creator.
	 *
	 * @param  string  $driver
	 * 
	 * @return mixed
	 * 
	 */
	protected function callCustomCreator($driver)
	{
		return $this->customCreators[$driver]($this->app);
	}

	/**
	 * 
	 * Register a custom driver creator Closure.
	 *
	 * @param  string    $driver
	 * @param  \Closure  $callback
	 * 
	 * @return $this
	 * 
	 */
	public function extend($driver,  Closure $callback)
	{
		$this->customCreators[$driver] = $callback;

		return $this;
	}

	/**
	 * 
	 * Get all of the created "drivers".
	 *
	 * @return array
	 * 
	 */
	public function getDrivers()
	{
		return $this->drivers;
	}

	/**
	 * 
	 * Dynamically call the default driver instance.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * 
	 * @return mixed
	 * 
	 */
	public function __call($method,  $parameters)
	{
		return $this->driver()->$method(...$parameters);
	}
}