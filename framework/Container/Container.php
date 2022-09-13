<?php
namespace Repository\Component\Container;

use Closure;
use ArrayAccess;
use ReflectionClass;
use Repository\Component\Container\Exception\BindingResolutionException;
use Repository\Component\Contracts\Container\ContainerInterface as IContainer;

/**
 * Inversion of Control Container.
 *
 * @package	  \Repository\Component\Container
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Container implements IContainer, ArrayAccess
{
	/**
	 * Binding container
	 * @var array $bindings
	 */
	protected $bindings = array();

	/**
	 * Instance container
	 * @var array $instances
	 */
	protected $instances = array();

	/**
	 * Resolved container
	 * @var array $resolved
	 */
	protected $resolved = array();

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::dropStaleInstance()
	 */
	public function dropStaleInstance($abstract)
	{
		unset($this->instances[$abstract]);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::isSingleton()
	 */
	public function isSingleton($abstract)
	{
		if(isset($this->bindings[$abstract]['singleton'])) {
			$singleton = $this->bindings[$abstract]['singleton'];
		} else {
			$singleton = false;
		}

		return isset($this->instances[$abstract]) || $singleton === true;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::isBuildable()
	 */
	public function isBuildable($concrete, $abstract)
	{
		return $concrete === $abstract || $concrete instanceof Closure;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::resolved()
	 */
	public function resolved($abstract)
	{
		return isset($this->resolved[$abstract]) || isset($this->instances[$abstract]);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::bound()
	 */
	public function bound($abstract)
	{
		return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::getConcrete()
	 */
	public function getConcrete($abstract)
	{
		if(!isset($this->bindings[$abstract])) {
			return $abstract;
		}
		return $this->bindings[$abstract]['concrete'];
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::registerDependencyManager()
	 */
	public function registerDependencyManager()
	{
		return new DependencyManager();
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::build()
	 */
	public function build($concrete, $parameters = array())
	{
		if ($concrete instanceof Closure) {
			return $concrete($this, $parameters);
		}

		$reflector = new ReflectionClass($concrete);

		if (! $reflector->isInstantiable()) {
			$message = "Target [$concrete] is not instantiable.";

			throw new BindingResolutionException($message);
		}

		$constructor = $reflector->getConstructor();

		if (is_null($constructor)) {
			return new $concrete;
		}

		$dependencies = $constructor->getParameters();
		$manager = $this->registerDependencyManager();
		$manager->keyParametersByArgument(
			$dependencies, $parameters
		);

		$instances = $manager->getDependencies(
			$this, $dependencies, $parameters
		);

		return $reflector->newInstanceArgs($instances);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::make()
	 */
	public function make($abstract, $parameters = array())
	{
		if (isset($this->instances[$abstract])) {
			return $this->instances[$abstract];
		}
		
		$concrete = $this->getConcrete($abstract);

		if ($this->isBuildable($concrete, $abstract)) {
			$object = $this->build($concrete, $parameters);
		} else {
			$object = $this->make($concrete, $parameters);
		}

		if ($this->isSingleton($abstract)) {
			$this->instances[$abstract] = $object;
		}

		$this->resolved[$abstract] = true;

		return $object;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::instance()
	 */
	public function instance($abstract, $instance)
	{
		$bound = $this->bound($abstract);

		$this->instances[$abstract] = $instance;

		if ($bound) {
			$this->make($abstract);
		}
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::bind()
	 */
	public function bind($abstract, $concrete, $singleton = false)
	{
		$this->dropStaleInstance($abstract);

		$concrete = (is_null($concrete))?$abstract:$concrete;
		
		$this->bindings[$abstract] = compact("concrete", "singleton");
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::signleton()
	 */
	public function singleton($abstract, $concrete = null)
	{
		$this->bind($abstract, $concrete, true);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface::bindIf()
	 */
	public function bindIf($abstract, $concrete, $singleton = false)
	{
		if(!$this->bound($abstract)) {
			$this->bind($abstract, $concrete, $singleton);
		}
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Container\ContainerInterface
	 */
	public function getBindings()
	{
		return $this->bindings;
	}

	/**
	 * Determine if a given offset exists
	 * 
	 * @param string $key
	 * 
	 * @return boolean
	 */
	public function offsetExists($key)
	{
		return isset($this->bindings[$key]);
	}

	/**
	 * Get a value at a given offset
	 * 
	 * @param string $key
	 * 
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->make($key);
	}

	/**
	 * Set a value at a given offset
	 * 
	 * @param string $key
	 * @param mixed #value
	 * 
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		if(!$value instanceof Closure) {
			$value = function () use ($value) {
				return $value;
			};
		}

		$this->bind($key, $value);
	}

	/**
	 * Unset the value of a given offset
	 * 
	 * @param string $key
	 * 
	 * @return boolean
	 */
	public function offsetUnset($key)
	{
		unset($this->bindings[$key], $this->instances[$key], $this->resolved[$key]);
	}

	/**
	 * Dynamically access container services
	 * 
	 * @param string $key
	 * 
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this[$key];
	}

	/**
	 * Dynamically set container services
	 * 
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this[$key] = $value;
	}
}