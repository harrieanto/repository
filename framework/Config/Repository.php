<?php
namespace Repository\Component\Config;

use Repository\Component\Support\Traits\Dot;
use Repository\Component\Contracts\Loader\LoaderInterface;

/**
 * Config Repository.
 *
 * @package	  \Repository\Component\Config
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Repository implements \ArrayAccess
{
	use Dot;

	/**
	 * The loader implementation.
	 * @var \Repository\Component\Contracts\Config\LoaderInterface
	 */
	protected $loader;

	/**
	 * All of the configuration items.
	 * @var array
	 */
	protected $items = array();

	/**
	 * Create a new repository instance.
	 *
	 * @return void
	 */
	function __construct(LoaderInterface $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Determine if the given configuration value exists.
	 *
	 * @param  string  $key
	 * 
	 * @return bool
	 */
	public function has($key)
	{
		$default = microtime(true);

		return $this->get($key, $default) !== $default;
	}

	/**
	 * Get the specified configuration value.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * 
	 * @return mixed
	 */
	public function get($key, $default = false)
	{
		list($group, $item) = (array) $this->parseKey($key);
		
		$this->load($group);

		if (empty($item)) {
			return $this->items[$group];
		}

		return Dot::get($this->items[$group], $item, $default);
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
		list($group, $item) = (array) $this->parseKey($key);

		$this->load($group);

		if (empty($item)) {
			$this->items[$group] = $value;
		} else {
			Dot::set($this->items[$group], $item, $value);
		}

		$this->loader->set($key, $value);
	}

	/**
	 * Load the configuration group for the key.
	 *
	 * @param string $group
	 * 
	 * @return void
	 */
	public function load($group)
	{
		if (isset($this->items[$group])) return;

		$this->items[$group] = $this->loader->load($group);
	}

	/**
	 * Parse a key into group, and item.
	 *
	 * @param  string  $key
	 * 
	 * @return array
	 */
	public function parseKey($key)
	{
		$segments = explode('.', $key);

		$group = $segments[0];

		unset($segments[0]);

		$segments = implode('.', $segments);

		return array($group, $segments);
	}

	/**
	 * Get all of the configuration items.
	 *
	 * @return array
	 */
	public function getItems()
	{
		$this->load();
		return $this->items;
	}

	/**
	 * Get the loader manager instance.
	 *
	 * @return \Repository\Component\Config\LoaderInterface
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * Determine if the given configuration option exists.
	 *
	 * @param  string  $key
	 * 
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return $this->has($key);
	}

	/**
	 * Get a configuration option.
	 *
	 * @param  string  $key
	 * 
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->get($key);
	}

	/**
	 * Set a configuration option.
	 *
	 * @param  string  $key
	 * @param  mixed  $value
	 * 
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		$this->set($key, $value);
	}

	/**
	 * Unset a configuration option.
	 *
	 * @param  string  $key
	 * 
	 * @return void
	 */
	public function offsetUnset($key)
	{
		$this->set($key, null);
	}
}