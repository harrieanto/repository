<?php
namespace Repository\Component\Cache;

use Repository\Component\Filesystem\Filesystem as Fs;
use Repository\Component\Contracts\Filesystem\FilesystemInterface;
use Repository\Component\Contracts\Cache\Store;

/**
 * Cache File Store Based.
 *
 * @package	  \Repository\Component\Cache
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class FileStore implements Store
{
	use CacheMultipleTrait;

	/**
	 * The cache directory name
	 * @var string
	 */
	protected $directory;

	/**
	 * The filesystem instance
	 * @var \Repository\Component\Contracts\Filesystem\FilesystemInterface
	 */	
	protected $fs;

	/**
	 * The cache prefix
	 * @var string
	 */
	protected $prefix;

	/**
	 * @param \Repository\Component\Contracts\Filesystem\FilesystemInterface $fs
	 * @param string $directory
	 * @param string $prefix
	 */	
	public function __construct(FilesystemInterface $fs, $directory, $prefix)
	{
		$this->fs = $fs;

		if (!$fs->isDirectory($directory)) {
			throw new \Exception("Directory [$directory] not found.");
		}
		
		$directory = SYSTEM_DIR_ROOT . trim($directory, DS) . DS;

		$this->directory = $directory;
		$this->prefix = trim($prefix, DS);
	}

	/**
	 * Retrieve items payload from the cache by the given key.
	 *
	 * @param  string|array  $key
	 * 
	 * @return mixed
	 */
	public function getPayload($key)
	{
		if (!$this->has($key)) return;

		$storage = $this->directory.$this->prefix.md5($key);
		$payload = $this->fs->getContent($storage);
		$payload = unserialize($payload);
		
		return $payload;
	}
	
	public function has($key)
	{
		$exist = file_exists($this->directory.$this->prefix.md5($key));
		
		return $exist ?? false;
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
		if (!$this->has($key)) return;

		$payloads = $this->getPayload($key);
		
		return $payloads;
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
		$storage = $this->directory.$this->prefix.md5($key);
		$payload = array('payload' => $value, 'expired' => $minutes);
		$payload = serialize($payload);

		$this->fs->putContent($storage, $payload);
	}

	/**
	 * Increment the value of an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * 
	 * @return int
	 */
	public function increment($key, $value = 1)
	{
		$payloads = $this->getPayload($key);
		$value = (int) $payloads['payload'] + (int) $value;
		
		$expired = isset($payloads['expired']) ?? 0;

		return $this->put($key, $value, $expired);
	}

	/**
	 * Decrement the value of an item in the cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * 
	 * @return int
	 */
	public function decrement($key, $value = 1)
	{
		return $this->increment($key, $value * -1);
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
		if (!$this->has($key)) return;
		
		$payloadTarget = $this->directory.$this->prefix.md5($key);
		$this->fs->deletes($payloadTarget);

		return true;
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @return bool
	 */
	public function flush()
	{
		foreach (glob($this->directory.'*') as $cacheFile) {
			$this->fs->deletes($cacheFile);
		}

		return true;
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