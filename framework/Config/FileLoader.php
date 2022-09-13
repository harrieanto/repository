<?php
namespace Repository\Component\Config;

use Repository\Component\Contracts\Loader\LoaderInterface;

/**
 * File Loader.
 *
 * @package	  \Repository\Component\Config
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class FileLoader implements LoaderInterface
{
	/**
	 * Create a new FileLoader instance.
	 */
	function __construct()
	{
	}

	/**
	 * Load the Configuration Group for the key.
	 *
	 * @param string $group
	 * 
	 * @return array
	 */
	public function load($group)
	{
		return Config::get($group, array());
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
		Config::set($key, $value);
	}
}