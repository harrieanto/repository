<?php
namespace Repository\Component\Routing;

use Psr\Http\message\UriInterface;
use Psr\Http\message\RequestInterface;
use Psr\Http\message\ResponseInterface;
use Repository\Component\Routing\Controller\Controller;
use Repository\Component\Routing\Exception\RouteException;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * Route Factory.
 * 
 * @package	  \Repository\Component\Routing
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Route extends Builder
{
	/** The host group type **/
	const HOST_GROUP = 'host';

	/** The path root type **/
	const ROOT_GROUP = 'root';

	/** The target route identifier **/
	const TARGET_NAME = 'target';

	/** The handler route identifier **/	
	const HANDLER_NAME = 'handler';

	/** The pipe character **/
	const PIPE = '|';

	/** The numeric pattern identifier **/
	const NUMERIC_TYPE = 'digit';

	/** The alphabetical pattern identifier **/
	const ALPHA_TYPE = 'alpha';

	/** The alphabet and numeric pattern identifier **/
	const ALNUM_TYPE = 'alnum';

	/** The any pattern identifier **/
	const ANY_TYPE = 'any';

	/** The lower pattern identifier **/
	const LOWER_TYPE = 'lower';

	/** The upper pattern identifier **/
	const UPPER_TYPE = 'upper';

	/**
	 * Route builder instance
	 *
	 * @var \Repository\Component\Routing\Builder $builder
	 */
	private $builder;

	/**
	 * Route dispatcher instance
	 *
	 * @var \Repository\Component\Routing\Dispatcher $dispatcher
	 */
	private $dispatcher;

	/**
	 * 
	 * @param \Repository\Component\Contracts\ContainerInterface
	 * @param \Psr\Http\Message\RequestInterface
	 * @param \Psr\Http\Message\ResponseInterface
	 * @param \Psr\Http\Message\UriInterface
	 * @param \Repository\Component\Routing\Controller\Controller
	 * @param \Repository\Component\Routing\MIddlewarePipeline
	 */
	public function __construct(
		ContainerInterface $container, 
		RequestInterface $request, 
		ResponseInterface $response, 
		UriInterface $uri, 
		Controller $controller, 
		MiddlewarePipeline $pipeline)
	{
		$this->app = $container;
		$this->request = $request;
		$this->response = $response;
		$this->uri = $uri;
		$this->controller = $controller;
		$this->pipeline = $pipeline;
		
		parent::__construct($container, $uri, $request, $response);
		$this->resolveDispatcher();
	}

	/**
	 * Create new route dispathcer instance
	 * 
	 * @return void
	 */
	private function resolveDispatcher()
	{
		$dispatcher = new Dispatcher(
			$this->app, 
			$this->request, 
			$this->response, 
			$this, 
			$this->controller, 
			$this->pipeline
		);

		$this->dispatcher = $dispatcher;
	}

	/**
	 * Dispatch current requsted route
	 * 
	 * @return \Repository\Component\Routing\Route
	 */
	public function dispatch()
	{
		$this->dispatcher->dispatch();
	}
}