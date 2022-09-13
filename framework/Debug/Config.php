<?php
namespace Repository\Component\Debug;

use Exception;
use Repository\Component\Foundation\Application;

/**
 * Handle Debug Configurations.
 *
 * @package	  \Repository\Component\Debug
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Config
{
	/**
	 * @inheritdoc
	 * @param \Repository\Component\Foundation\Application $app
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * Get debug configuration by the given group name
	 * 
	 * @param string $group
	 * 
	 * @return array|string
	 */
	private function getDebugParameter($group)
	{
		$debug = $this->app['config']['debug'][$group];
		
		return $debug;
	}

	/**
	 * Get throwbale indication from environment configuration
	 * 
	 * @return bool True Exception is thrown
	 * False otherwise
	 */	
	public function getThrowableIndication()
	{
		//Since our environment configuration value formatted as string
		//we have to determine if the true/false configuration passed and convert back to boolean value
		$env = $this->app['config']['application']['environment'];
		$params = $this->getDebugParameter($env);
		
		switch ($params['display_error']) {
			case 'true':
				$throwable = true;
			break;
			case 'false':
				$throwable = false;
			break;
			default:
				$ex = "The given value app environemnt is not identified.";
				throw new \RuntimeException($ex);
		}

		return $throwable;
	}
	
	/**
	 * Determine if the exception alert enable
	 * 
	 * @return bool True Exception is thrown
	 * False otherwise
	 */	
	public function isThrowableAlertEnable()
	{
		if ($this->getThrowableIndication()) return true;
		
		return false;
	}
}