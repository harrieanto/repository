<?php
namespace Repository\Component\Config;

use Repository\Component\Support\Traits\Dot;

/**
 * The Config Static Locator.
 *
 * @package	  \Repository\Component\Config
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Config
{
	use Dot;

	/**
	 * Configurations
	 * @var array
	 */
	protected static $settings = array();


	/**
	 * Get the registered settings.
	 * 
	 * @return mixed|array
	 */
	public static function all()
	{
		return static::$settings;
	}

	/**
	 * Return true if the key exists.
	 * 
	 * @param string $key
	 * 
	 * @return bool
	 */
	public static function has($key)
	{
		return ! is_null(Dot::get(static::$settings, $key));
	}

	/**
	 * Get the value.
	 * 
	 * @param string $key
	 * 
	 * @return mixed|null
	 */
	public static function get($key, $default = null)
	{
		return Dot::get(static::$settings, $key, $default);
	}

	/**
	 * Set the value.
	 * 
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @return void
	 */
	public static function set($key, $value)
	{
		Dot::set(static::$settings, $key, $value);
	}
}