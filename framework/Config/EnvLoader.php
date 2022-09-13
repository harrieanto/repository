<?php
namespace Repository\Component\Config;

use Repository\Component\Contracts\Container\ContainerInterface;
use Repository\Component\Contracts\Loader\LoaderInterface;
use Repository\Component\Environment\Environment;

/**
 * Environment Loader.
 *
 * @package	  \Repository\Component\Config
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class EnvLoader implements LoaderInterface
{
	public function __construct(ContainerInterface $app, $envFile, $global = false)
	{
		$this->environment = new Environment($app, $envFile, $global);
		$this->environment->setUp();
	}
	
	/**
	 * Load the Configuration Group for the key.
	 *
	 * @param string $group
	 * 
	 * @return mixed
	 */
	public function load($group)
	{
		return $this->environment->getEnvironmentVariable($group);
	}

	/**
	 * Set a given configuration value.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * 
	 * @return void
	 */
	public function set($key, $value)
	{
		$this->environment->setEnvironmentVariable($key, $value);
	}
}