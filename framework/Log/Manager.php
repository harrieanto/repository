<?php
namespace Repository\Component\Log;

use Repository\Component\Support\Manager as LogManager;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * Logger Manager
 *
 * @package	  \Repository\Component\Log
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Manager extends LogManager
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
		$driver = $this->getLoggerParameter('channel');
		
		return $driver;
	}
	
	/**
	 * Get logger parameter by the given key
	 * 
	 * @param string $key
	 * 
	 * @return string
	 */	
	private function getLoggerParameter($key)
	{
		$param = $this->app['config']['log'][$key];
		
		return $param;
	}

	/**
	 * Create stream logger
	 * 
	 * @return \Psr\Log\LoggerInterface
	 */
	public function createStreamDriver()
	{
		$logger = new Logger();
		$storage = $this->getLoggerParameter('storage');
		$logger->setLogger(new StreamLogger($this->app['fs'], $storage));
		
		return $logger;
	}
}