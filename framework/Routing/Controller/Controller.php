<?php
namespace Repository\Component\Routing\Controller;

use ReflectionMethod;
use ReflectionFunction;
use Repository\Component\Contracts\Container\ContainerInterface;
use Repository\Component\Routing\Controller\Exception\ControllerException;

/**
 * Base Controller.
 * 
 * @package	  \Repository\Component\Routing
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Controller
{
	/** The default method name **/
	const DEFAULT_METHOD = 'index';

	/**
	 * Container instance
	 *
	 * @var \Repository\Component\Contracts\ContainerInterface $app
	 */
	protected $app;

	/**
	 * Route target container
	 *
	 * @var array $classTargets
	 */
	protected static $classTargets = array();

	/**
	 * Class names container
	 *
	 * @var array $methodNames
	 */
	protected static $classNames = array();

	/**
	 * Method names container
	 *
	 * @var array $methodNames
	 */
	protected static $methodNames = array();

	public function __construct()
	{
		//
	}

	/**
	 * Register application framework instance to the controller
	 * 
	 * @return void
	 */	
	public function registerApplication(ContainerInterface $app)
	{
		$this->app = $app;
		$this->boot();
	}


	/**
	 * Boot any functions to the controller on startup
	 * 
	 * @return void
	 */	
	public function boot()
	{
		//
	}

	/**
	 * Boot any functions to the controller before loaded/startup
	 * 
	 * @return void
	 */	
	public static function beforeMount()
	{
		//
	}

	/**
	 * Resolve controller class dependencies
	 * 
	 * @param string $abstract
	 * @param mixed $concrete
	 * 
	 * @throw \Repository\Component\Routing\Controller\Exception\ControllerException
	 *  
	 * @return void
	 */	
	public function resolveControllerClass($abstract, $concrete)
	{
		$this->app->bindIf($abstract, $concrete);
	}
	
	/**
	 * Resolve controller method dependencies
	 * 
	 * @param \ReflectionMethod|\ReflectionFunction $reflector
	 * @param array $primitives
	 * 
	 * @throw \Repository\Component\Routing\Controller\Exception\ControllerException
	 *  
	 * @return null
	 */	
	public function resolveControllerHandler($reflector, $abstract, $primitives = array())
	{
		//Register automatic dependency resolution
		$dependency = $this->app->registerDependencyManager();
		//Get paremeter from request dependency
		$parameters = $reflector->getParameters();

		//Resolve dependencies
		switch($primitives) {
			//If primitives parameter exist
			case count($primitives) > 0:
				$params = array();
				$index = 0;
				
				//Rekey parameters dependency
				//by the given primitives parameter
				foreach($parameters as $param) {

					$class = $param->getClass();

					if(is_object($class)) {
						$params[$param->name] = $this->app[$class->name];
					} else {
						if (!isset($primitives[$index])) {
							if ($param->isOptional() || $param->isDefaultValueAvailable()) {
								$primitive = $param->getDefaultValue();
							} else {
								throw new ControllerException("Method parameter unresolvable.");
							}
						} else {
							$primitive = $primitives[$index];
						}

	 					$params[$param->name] = $primitive;
	 					$index++;
					}
				}

				//Build dependency
				$dependencies = $dependency->getDependencies(
					$this->app, 
					$parameters, 
					$params
				); break;
			default:
				$dependencies = $dependency->getDependencies($this
					->app, $reflector
					->getParameters()
				);
		}

		if ($reflector instanceof ReflectionMethod) {
			$controller = $this->app[$abstract];
			
			if (!$controller instanceof Controller) {
				$controller = Controller::class;
				$exception = "Controller must be instance of [$controller]. ";
				$exception.= "Another Given.";
				
				throw new ControllerException($exception);
			}

			$controller->registerApplication($this->app);

			$dependency->resolveMethod(
				$reflector, 
				$controller, 
				$dependencies
			);
			
			return;
		}
		
		if ($reflector instanceof ReflectionFunction) {
			return call_user_func_array($abstract, $dependencies);
		}
	}

	/**
	 * @param string $alias Route alias
	 * @param string $methodName
	 *  
	 * @return \Repository\Component\routing\Controller\Controller
	 */	
	public static function setMethodName($alias, $methodName)
	{
		self::$methodNames[$alias] = $methodName;
		
		return new self;
	}

	/**
	 * Get current method name by the given alias
	 * 
	 * @param string $alias
	 *  
	 * @return string Method name
	 */	
	public static function getMethodName($alias)
	{
		return self::$methodNames[$alias];
	}

	/**
	 * @param string $alias Route alias
	 * @param string $className
	 *  
	 * @return \Repository\Component\routing\Controller\Controller
	 */	
	public static function setClassName($alias, $className)
	{
		self::$classNames[$alias] = $className;
		
		return new self;
	}

	/**
	 * Get current class name by the given alias
	 * 
	 * @param string $alias
	 *  
	 * @return string Class name
	 */	
	public static function getClassName($alias)
	{
		return self::$classNames[$alias];
	}

	/**
	 * @param string $alias Route alias
	 * @param string $target
	 *  
	 * @return \Repository\Component\routing\Controller\Controller
	 */	
	public static function setTarget($alias, $target)
	{
		self::$classTargets[$alias] = $target;
		
		return new self;
	}

	/**
	 * Get route target by the given alias
	 * 
	 * @param string $alias
	 *  
	 * @return string Target url
	 */	
	public static function getTarget($alias)
	{
		return self::$classTargets[$alias];
	}
}