<?php
namespace Repository\Component\Contracts\Cache;

/**
 * Cache Store Interface.
 * 
 * @package	 \Repository\Component\Contracts\Cache
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface Store
{
	/**
	 * Retrieve an item from the cache by key.
	 *
	 * @param string|array $key
	 * 
	 * @return mixed
	 */
	public function get($key);

	/**
	 * Retrieve multiple items from the cache by key.
	 *
	 * Items not found in the cache will have a null value.
	 *
	 * @param array $keys
	 * 
	 * @return array
	 */
	public function many(array $keys);

	/**
	 * Store an item in the cache for a given number of minutes.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param float|int $minutes
	 * 
	 * @return void
	 */
	public function put($key, $value, $minutes);

	/**
	 * Store multiple items in the cache for a given number of minutes.
	 *
	 * @param array $values
	 * @param float|int $minutes
	 * 
	 * @return void
	 */
	public function putMany(array $values, $minutes);

	/**
	 * Increment the value of an item in the cache.
	 *
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @return int|bool
	 */
	public function increment($key, $value = 1);

	/**
	 * Decrement the value of an item in the cache.
	 *
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @return int|bool
	 */
	public function decrement($key, $value = 1);

	/**
	 * Store an item in the cache indefinitely.
	 *
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @return void
	 */
	public function forever($key, $value);

	/**
	 * Remove an item from the cache.
	 *
	 * @param string $key
	 * 
	 * @return bool
	 */
	public function forget($key);

	/**
	 * Remove all items from the cache.
	 *
	 * @return bool
	 */
	public function flush();

	/**
	 * Get the cache key prefix.
	 *
	 * @return string
	 */
	public function getPrefix();
}