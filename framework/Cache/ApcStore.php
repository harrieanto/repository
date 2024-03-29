<?php
namespace Repository\Component\Cache;

use Repository\Component\Contracts\Cache\Store;

/**
 * Cache APC Store based.
 *
 * @package	  \Repository\Component\Cache
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ApcStore implements Store
{
	use CacheMultipleTrait;

	/**
	 * The APC wrapper instance.
	 * @var \Repository\Component\Cache\ApcWrapper
	 */
	protected $apc;

	/**
	 * A string that should be prepended to keys.
	 * @var string
	 */
	protected $prefix;

	/**
	 * Create a new APC store.
	 *
	 * @var \Repository\Component\Cache\Apc $apc
	 * @param  string  $prefix
	 * 
	 * @return void
	 */
	public function __construct(Apc $apc, $prefix = '')
	{
		$this->apc = $apc;
		$this->prefix = $prefix;
	}

	/**
	 * Retrieve an item from the cache by key.
	 *
	 * @param  string|array  $key
	 * 
	 * @return mixed
	 */
	public function get($key)
	{
		$value = $this->apc->get($this->prefix.$key);

		if ($value !== false) {
			return $value;
		}
	}

	/**
	 * Store an item in the cache for a given number of minutes.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  float|int  $minutes
	 * 
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		$this->apc->put($this->prefix.$key, $value, (int) ($minutes * 60));
	}

	/**
	 * Increment the value of an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * 
	 * @return int|bool
	 */
	public function increment($key, $value = 1)
	{
		return $this->apc->increment($this->prefix.$key, $value);
	}

	/**
	 * Decrement the value of an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * 
	 * @return int|bool
	 */
	public function decrement($key, $value = 1)
	{
		return $this->apc->decrement($this->prefix.$key, $value);
	}

	/**
	 * Store an item in the cache indefinitely.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * 
	 * @return void
	 */
	public function forever($key, $value)
	{
		$this->put($key, $value, 0);
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param  string  $key
	 * 
	 * @return bool
	 */
	public function forget($key)
	{
		return $this->apc->delete($this->prefix.$key);
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @return bool
	 */
	public function flush()
	{
		return $this->apc->flush();
	}

	/**
	 * Get the cache key prefix.
	 *
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}
}