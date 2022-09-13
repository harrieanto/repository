<?php
namespace Repository\Component\Contracts\Container;

interface ContainerInterface
{
	/**
	 * Unset all instance that have been bound with abstract type
	 * 
	 * @param string $abstract
	 * 
	 * @return void
	 */
	public function dropStaleInstance($abstract);

	/**
	 * Determine if the given abstract type is singleton
	 * 
	 * @param string $abstract
	 * 
	 * @return bool
	 */
	public function isSingleton($abstract);

	/**
	 * Determine if the given conrete type is buildable
	 * 
	 * @param mixed $concrete
	 * @param string $abstract
	 *  
	 * @return bool
	 */
	public function isBuildable($concrete, $abstract);

	/**
	 * Determine if the given abstract type have been resolved
	 * 
	 * @param string $abstract
	 * 
	 * @return bool
	 */
	public function resolved($abstract);

	/**
	 * Determine if the given abstract type have been bound within container
	 * 
	 * @param string  $abstract
	 * 
	 * @return boolean
	 */
	public function bound($abstract);

	/**
	 * Get concrete type of the given abstarct type
	 * 
	 * @param  string  $abstract
	 * 
	 * @return mixed   $concrete
	 */
	public function getConcrete($abstract);

	/**
	 * Register automatic dependency resolution to the container
	 * 
	 * @return void
	 */
	public function registerDependencyManager();

	/**
	 * Build an instance of the given concrete and primitive parameters
	 *
	 * @param  string  $concrete
	 * @param  array   $parameters
	 * 
	 * @throws \Repository\Component\Container\Exception\BindingResolutionException
	 * 
	 * @return mixed
	 */
	public function build($concrete, $parameters = array());

	/**
	 * Resolve the given abstract type
	 *
	 * @param  string  $abstract
	 * @param  array   $parameters The list of primitive parameters being used for resolve an object
	 * 
	 * @return mixed
	 */
	public function make($abstract, $parameters = array());

	/**
	 * Register user defined instance to the container
	 *
	 * @param  string  $abstract
	 * @param  mixed   $instance
	 * 
	 * @return void
	 */
	public function instance($abstract, $instance);

	/**
	 * Register abstract and concrete binding to the container
	 * 
	 * @param string|array $abstract
	 * @param Closure|string|null $concrete
	 * @param boolean $singleton
	 */
	public function bind($abstract, $concrete, $singleton = false);

	/**
	 * Register the given abstract type as singleton to the container
	 * 
	 * @param string $abstract
	 * @param Closure|string|null $concrete
	 * 
	 * @return void
	 */
	public function singleton($abstract, $concrete = null);

	/**
	 * Register binding if the given abstract type not registered before
	 * 
	 * @param string $abstract
	 * @param Closure|string|null $concrete
	 * 
	 * @return void
	 */
	public function bindIf($abstarct, $concrete, $singleton = false);
}