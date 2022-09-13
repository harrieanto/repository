<?php
namespace Repository\Component\Support\Statics;

use Repository\Component\Foundation\Application;
use RuntimeException;

/**
 * Invoke Framework Component As Static.
 * 
 * @package	  \Repository\Component\Session
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
abstract class InvokeStatic
{
	/**
	 * The application instance being static.
	 * @var \Repository\Component\Contract\Foundation\Application
	 */
	protected static $app;

	/**
	 * Resolved static instance container
	 * @var array
	 */
	protected static $resolvedStaticInstance;

	/**
	 * Populete static aliases
	 * @var array
	 */
	protected static $aliases = [];


	/**
	 * Get static instance
	 * 
	 * @return mixed
	 */
	public static function getStaticInstance()
	{
		return static::resolveInstance(static::getStaticAccesor());
	}

	/**
	 * Resolve static instance
	 * 
	 * @param string $name
	 * 
	 * @return mixed
	 */
	public static function resolveInstance($name)
	{
		if (is_object($name)) {
			return $name;
		}

		if (isset(static::$resolvedStaticInstance[$name])) {
			return static::$resolvedStaticInstance[$name];
		}

		static::$resolvedStaticInstance[$name] = static::$app->make(static::getConcrete($name));

		return static::$resolvedStaticInstance[$name];
	}

	/**
	 * Get concrete class name by the given alias name
	 * 
	 * @param  string $name The name of the concrete class being called static
	 * 
	 * @return mixed
	 */
	public static function getConcrete($name)
	{
		return static::getAliases($name);
	}

	/**
	 * Resolve static aliases from configuration container
	 * 
	 * @param  string $name
	 * 
	 * @return string
	 */
	public static function getAliases($name)
	{
		$aliases = static::$app['config']['application']['aliases'];

		array_map (function($alias, $concrete) {

			static::$aliases[strtolower($alias)] = $concrete;

		}, array_keys($aliases), array_values($aliases));
		
		if (is_array(static::$aliases) && array_key_exists($name, static::$aliases)) {
			return static::$aliases[$name];
		}

		$ex = "The alias [$name] being called static not found in the configuration. Make sure you have specify it correctly";
		throw new RuntimeException($ex);
	}

	/**
	 * Clear one specific resolved instance
	 * 
	 * @param  string $name string
	 * 
	 * @return void
	 */
	public static function clearResolvedInstance($name)
	{
		unset(static::$resolvedStaticInstance[$name]);
	}

	/**
	 * Clear resolved instances
	 * 
	 * @return void
	 */
	public static function clearResolvedInstances()
	{
		static::$resolvedStaticInstance = [];
	}

	/**
	 * Set static application
	 * 
	 * @param \Repository\Component\Foundation\Application $app
	 *  
	 * @return void
	 */
	public static function setStaticApplication(Application $app)
	{
		static::$app = $app;
	}

	/**
	 * Get static application
	 * 
	 * @return mixed
	 */
	public static function getStaticApplication()
	{
		return static::$app;
	}

	/**
	 * Get the registered name of the component being called static.
	 *
	 * @return string
	 *
	 * @throws \RuntimeException
	 */
	public static function getStaticAccesor()
	{
		throw new RuntimeException("Static accesor doesn't implement [getStaticAccesor]");
	}

	/**
	 * Handle dynamic, static calls to the object.
	 *
	 * @param  string  $method
	 * @param  array   $args
	 * 
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		$instance = static::getStaticInstance();

		if (!$instance) {
			throw new RuntimeException('A static accesor has not been set.');
		}
		
		return call_user_func_array([$instance, $method], $args);
	}
}