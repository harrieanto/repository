<?php
namespace Repository\Component\Session\Handlers;

use Repository\Component\Cache\Repository;
use Repository\Component\Contracts\Cache\Store;
use Repository\Component\Support\Manager as SessionManager;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * Session Manager.
 * 
 * @package	  \Repository\Component\Session
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Manager extends SessionManager
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
		$driver = $this->getSessionParameter('driver');
		
		return $driver;
	}
	
	/**
	 * Get session parameter by the given key
	 * 
	 * @param string $key
	 * 
	 * @return string
	 */	
	private function getSessionParameter($key)
	{
		$param = $this->app['config']['session'][$key];
		
		return $param;
	}

	/**
	 * Create session by file storage
	 * 
	 * @return \SessionHandlerInterface
	 */
	public function createFileDriver()
	{
		$sessions = $this->app['config']['session'];
		$session = new FileSessionHandler($sessions['pathfile']);
		$session->setPrefix($sessions['prefix']);
		$session->useEncryption($sessions['encrypted']);
		$session->setEncryptionHandler($this->app['encryption']);
		
		return $session;
	}

	/**
	 * Create session apc driver
	 * 
	 * @return \SessionHandlerInterface
	 */
	public function createApcDriver()
	{
		return $this->createCacheHandler('apc');
	}

	/**
	 * Create session cache handler
	 * 
	 * @return \SessionHandlerInterface
	 */
	public function createCacheHandler(string $driver)
	{
		$sessions = $this->app['config']['session'];

		$session = new CacheSessionHandler(
			$this->app['cache']->driver($driver), 
			$sessions['lifetime']
		);

		$session->setPrefix($sessions['prefix']);
		$session->useEncryption($sessions['encrypted']);
		$session->setEncryptionHandler($this->app['encryption']);
		
		return $session;
	}
}