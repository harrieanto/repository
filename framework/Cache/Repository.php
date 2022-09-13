<?php
namespace Repository\Component\Cache;

use Closure;
use DateTime;
use ArrayAccess;
use Carbon\Carbon;
use Repository\Component\Validation\Filter;
use Repository\Component\Contracts\Cache\Store;

/**
 * Cache Repository.
 *
 * @package	  \Repository\Component\Cache
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Repository implements ArrayAccess
{
	/**
	 * The cache store implementation.
	 * @var \Repository\Component\Contracts\Cache\Store
	 */
	protected $store;

	/**
	 * The default number of minutes to store items.
	 * @var int
	 */
	protected $defaultCacheTime = 60;

	/**
	 * Create a new cache repository instance.
	 *
	 * @param  \Repository\Component\Contracts\Cache\Store  $store
	 */
	public function __construct(Store $store)
	{
		$this->store = $store;
	}

	/**
	 * Determine if an item exists in the cache.
	 *
	 * @param  string  $key
	 * 
	 * @return bool
	 */
	public function has($key)
	{
		return ! Filter::isNull($this->get($key));
	}

	/**
	 * Retrieve an item from the cache by key.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * 
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		$value = $this->store->get($key);
		
		if (!Filter::isNull($value)) return $value;
		
		return $this->getValueByDefault($default);
	}

	/**
	 * Retrieve an default value by the given default parameter.
	 *
	 * @param  mixed   $value
	 * 
	 * @return mixed
	 */	
	private function getValueByDefault($value)
	{
		$value = $value instanceof Closure ? $value() : $value;
		
		return $value;
	}

	/**
	 * Retrieve an item from the cache and delete it.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * 
	 * @return mixed
	 */
	public function pull($key, $default = null)
	{
		$value = $this->get($key, $default);

		$this->forget($key);

		return $value;
	}

	/**
	 * Store an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  \DateTime|int  $minutes
	 * 
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		$minutes = $this->getMinutes($minutes);

		if (!Filter::isNull($minutes)) {
			$this->store->put($key, $value, $minutes);
		}
	}

	/**
	 * Store an item in the cache if the key does not exist.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  \DateTime|int  $minutes
	 * 
	 * @return bool
	 */
	public function add($key, $value, $minutes)
	{
		if (Filter::isNull($this->get($key))) {
			$this->put($key, $value, $minutes); return true;
		}

		return false;
	}

	/**
	 * Get an item from the cache, or store the default value.
	 *
	 * @param  string  $key
	 * @param  \DateTime|int  $minutes
	 * @param  \Closure  $callback
	 * 
	 * @return mixed
	 */
	public function remember($key, $minutes, Closure $callback)
	{
		// If the item exists in the cache we will just return this immediately
		// otherwise we will execute the given Closure and cache the result
		// of that execution for the given number of minutes in storage.
		if (!Filter::isNull($value = $this->get($key))) {
			return $value;
		}

		$this->put($key, $value = $callback(), $minutes);

		return $value;
	}

	/**
	 * Get the default cache time.
	 *
	 * @return int
	 */
	public function getDefaultCacheTime()
	{
		return $this->defaultCacheTime;
	}

	/**
	 * Set the default cache time in minutes.
	 *
	 * @param  int   $minutes
	 * 
	 * @return void
	 */
	public function setDefaultCacheTime($minutes)
	{
		$this->defaultCacheTime = $minutes;
	}

	/*
	 * Get the cache store implementation.
	 *
	 * @return \Repository\Component\Contracts\Cache\Store
	 */
	public function getStore()
	{
		return $this->store;
	}

	/**
	 * Determine if a cached value exists.
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
	 * Retrieve an item from the cache by key.
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
	 * Store an item in the cache for the default time.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * 
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		$this->put($key, $value, $this->default);
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param  string  $key
	 * 
	 * @return void
	 */
	public function offsetUnset($key)
	{
		return $this->forget($key);
	}

	/**
	 * Calculate the number of minutes with the given duration.
	 *
	 * @param  \DateTime|int  $duration
	 * 
	 * @return int|null
	 */
	protected function getMinutes($duration)
	{
		if ($duration instanceof DateTime) {
			$fromNow = Carbon::instance($duration)->diffInMinutes();

			return $fromNow > 0 ? $fromNow : null;
		}

		return is_string($duration) ? (int) $duration : $duration;
	}

	/**
	 * Handle dynamic calls to the store.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * 
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$target = array($this->store, $method);
		return call_user_func_array($target, $parameters);
	}
}