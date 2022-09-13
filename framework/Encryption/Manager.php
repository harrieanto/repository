<?php
namespace Repository\Component\Encryption;

use Repository\Component\Support\Encoder;
use Repository\Component\Support\Manager as EncryptionManager;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * Encryption Manager.
 *
 * @package	  \Repository\Component\Encryption
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Manager extends EncryptionManager
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
		$driver = $this->getEncryptionParameter('type');
		
		return $driver;
	}
	
	/**
	 * Get encryption parameter by the given key
	 * 
	 * @param string $key
	 * 
	 * @return string
	 */	
	private function getEncryptionParameter($key)
	{
		$param = $this->app['config']['encryption'][$key];
		
		return $param;
	}

	/**
	 * Create openssl encryption
	 * 
	 * @return \Repository\Component\Contracts\Encryption\EncryptionInterface
	 */
	public function createOpensslDriver()
	{
		$key = $this->getEncryptionParameter('key');
		$cipher = $this->getEncryptionParameter('cipher');
		$openssl = new OpensslEncrypt($key, new Encoder, $cipher);
		
		return $openssl;
	}
}