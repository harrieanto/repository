<?php
namespace Repository\Component\Cache;

/**
 * Cache Multiple Trait.
 *
 * @package	  \Repository\Component\Cache
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
trait CacheMultipleTrait
{
	/**
	 * Retrieve multiple items from the cache by key.
	 *
	 * Items not found in the cache will have a null value.
	 *
	 * @param  array  $keys
	 * 
	 * @return array
	 */
	public function many(array $keys)
	{
		$return = [];

		foreach ($keys as $key) {
			$return[$key] = $this->get($key);
		}

		return $return;
	}

	/**
	 * Store multiple items in the cache for a given number of minutes.
	 *
	 * @param  array  $values
	 * @param  float|int  $minutes
	 * 
	 * @return void
	 */
	public function putMany(array $values, $minutes)
	{
		foreach ($values as $key => $value) {
			$this->put($key, $value, $minutes);
		}
	}
}