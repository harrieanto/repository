<?php
namespace Repository\Component\Cache;

use Repository\Component\Support\Manager as CacheManager;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * Cache Manager.
 *
 * @package	  \Repository\Component\Cache
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Manager extends CacheManager
{
	/**
	 * @inheritdoc
	 * See \Repository\Component\Support\Manager
	 */	
	public function __construct(ContainerInterface $app)
	{
		$this->app = $app;
	}
	
	/**
	 * @inheritdoc
	 * See \Repository\Component\Support\Manager::getDefaultDriver
	 */	
	public function getDefaultDriver()
	{
		$driver = $this->getCacheParameter('driver');
		
		return $driver;
	}
	
	/**
	 * Get logger parameter by the given key
	 * 
	 * @param string $key
	 * 
	 * @return string
	 */	
	private function getCacheParameter($key)
	{
		$param = $this->app['config']['cache'][$key];
		
		return $param;
	}

	/**
	 * Create an instance of the APC cache driver
	 * 
	 * @return \Repository\Component\Contracts\Ccahe\Store
	 */
	public function createApcDriver()
	{
		$prefix = $this->getCacheParameter('prefix');

		return new Repository(new ApcStore(new Apc, $prefix));
	}

	/**
	 * Create stream logger
	 * 
	 * @return \Psr\Log\LoggerInterface
	 */
	public function createFileDriver()
	{
		$prefix = $this->getCacheParameter('prefix');
		$storage = $this->getCacheParameter('basepath');
		$store = new FileStore($this->app['fs'], $storage, $prefix);

		return new Repository($store);
	}
}