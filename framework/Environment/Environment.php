<?php
namespace Repository\Component\Environment;

use Repository\Component\Contracts\Container\ContainerInterface;
use Repository\Component\Environment\Exception\EnvironmentException;

/**
 * Environemt Setting.
 *
 * @package	  \Repository\Component\Environment
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Environment
{
	/**
	 * The container instance
	 * @var \Repository\Component\Contracts\Container\ContainerInterface $app
	 */
	protected $app;
	
	/**
	 * The dot env path file
	 * @var string $envFile
	 */
	protected $envFile;

	/**
	 * The global environment indicator
	 * @var bool $globalEnvironment
	 */
	protected $gobalEnvironment = false;

	/**
	 * @param \Repository\Component\Contracts\Container\ContainerInterface $app
	 * @param string $envFile
	 * @param bool $global
	 */
	public function __construct(ContainerInterface $app, $envFile, bool $global = false)
	{
		$this->app = $app;
		$this->envFile = $envFile;
		$this->setGlobalEnvironment($global);
		$this->isValidEnvFile($envFile);
	}

	/**
	 * Setup .env file and produce to the global environment
	 * 
	 * @return \Repository\Component\Environment\Environment
	 */
	public function setUp()
	{
		$environments = $this->getEnvironmentAsArray($this->envFile);

		foreach ($environments as $env) {
			if (!$this->isComment($env) && $this->isEquals($env) !== false){
				$part  = implode(' ', array_map('nl2br', explode(' ',  $env)));
				$key = substr($part, 0, -strlen($value = strrchr($part,  '=')));
				$value = substr($value, 1, strlen($part));
				$this->setEnvironmentVariable($key,  $value);
			}
		}
		
		return $this;
	}

	/**
	 * Read the dot(.) env file as array raw
	 * 
	 * @param  string $pathfile The location of .env file
	 * 
	 * @return array
	 */
	protected function getEnvironmentAsArray($pathfile)
	{
		return  $this->getFs()->fileToArray($pathfile);
	}

	/**
	 * Determine if the given file is valid .env
	 * 
	 * @param  string $file 
	 * 
	 * @throw \Repository\Component\Environment\Exception\EnvironmentException
	 * 
	 * @return void
	 */
	public function isValidEnvFile($file)
	{
		$env = trim($this->app['request']->getLastPath($file), '/');

		if(!('.env' === $env) && !$this->getFs()->exists($file)) {
			throw new EnvironmentException('File .env not found');
		}
		
		return true;
	}

	/**
	 * Determine if the given string is comment
	 * 
	 * @param  string  $line The string that want validate
	 * 
	 * @return boolean
	 */
	protected function isComment($line)
	{
		return strpos(trim($line), '#');
	}

	/**
	 * Determine if the given string is equal sign
	 * 
	 * @param  string  $line The string that want validate
	 * 
	 * @return boolean
	 */
	protected function isEquals($line)
	{
		return strpos($line, '=');
	}

	/**
	 * Get environment variable that has definied in the .env
	 * and registered in thr global environment $_ENV
	 * 
	 * @param  string $name The name of variable
	 * 
	 * @return string|resource
	 */
	public function getEnvironmentVariable($name)
	{
		switch (true) {
			case array_key_exists($name, $_ENV):
				return $_ENV[$name];
			case array_key_exists($name, $_SERVER):
				return $_SERVER[$name];
			default:
				$environment = getenv($name);
				return (!$environment) ? null : $environment;
		}
	}

	/**
	 * Register environment to $_ENV and $_SERVER
	 * 
	 * @param string $name The key of $_ENV and $_SERVER
	 * @param string $value The value for the given key
	 * 
	 * @return void
	 */
	public function setEnvironmentVariable($name, $value)
	{
		if ($this->isGlobalEnvironment() && $this->getEnvironmentVariable($name) !== null) {
			return;
		}

		putenv('$name=$value');

		$_ENV[$name]    = $value;
		$_SERVER[$name] = $value;
	}
	
	/**
	 * Set status global environment
	 * 
	 * @param bool $global
	 * 
	 * @return void
	 */
	public function setGlobalEnvironment($global = false)
	{
		$this->globalEnvironment = $global;
	}

	/**
	 * Get status global environment
	 *  
	 * @return bool
	 */
	public function isGlobalEnvironment()
	{
		return $this->globalEnvironment;
	}
	
	/**
	 * Get Filesystem instance
	 * 
	 * @return \Repository\Component\Filesystem\Filesystem
	 */
	public function getFs()
	{
		return $this->app['fs'];
	}
}