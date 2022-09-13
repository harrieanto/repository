<?php
namespace Repository\Component\Routing;

use Closure;
use ReflectionMethod;
use Psr\Http\Message\UriInterface;
use Repository\Component\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Repository\Component\Collection\Collection;
use Repository\Component\Routing\Controller\Controller;
use Repository\Component\Contracts\Container\ContainerInterface;
use Repository\Component\Http\Exception\HttpException;
use Repository\Component\Http\Exception\NotFoundHttpException;

/**
 * Route Dispatcher.
 * 
 * @package	  \Repository\Component\Routing
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Dispatcher
{
	/**
	 * Container instance
	 *
	 * @var \Repository\Component\Contracts\ContainerInterface $app
	 */
	private $app;

	/**
	 * Request instance
	 *
	 * @var \Psr\Http\Message\RequestInterface $request
	 */
	private $request;

	/**
	 * Response instance
	 *
	 * @var \Psr\Http\Message\ResponseInterface $response
	 */
	private $response;
	
	/**
	 * Uri instance
	 *
	 * @var \Psr\Http\Message\UriInterface
	 */
	private $uri;

	/**
	 * Route builder instance
	 *
	 * @var \Repository\Component\Routing\Builder $builder
	 */
	private $builder;

	/**
	 * Base controller instance
	 *
	 * @var \Repository\Component\Routing\Controller\Controller
	 */
	protected $controller;

	/**
	 * Middleware pipeline instance
	 *
	 * @var \Repository\Component\Routing\MiddlewarePipeline
	 */
	protected $pipeline;

	/**
	 * 
	 * @param \Repository\Component\Contracts\ContainerInterface
	 * @param \Psr\Http\Message\RequestInterface
	 * @param \Psr\Http\Message\ResponseInterface
	 * @param \Psr\Http\Message\UriInterface
	 * @param \Repository\Component\Routing\Builder
	 * @param \Repository\Component\Routing\Controller\Controller
	 */
	public function __construct(
		ContainerInterface $app, 
		RequestInterface $request, 
		ResponseInterface $response, 
		Builder $builder, 
		Controller $controller, 
		MiddlewarePipeline $pipeline)
	{
		$this->app = $app;
		$this->request = $request;
		$this->response = $response;
		$this->builder = $builder;
		$this->controller = $controller;
		$this->pipeline = $pipeline;
	}

	/**
	 * Dispatch current route request through through pipeline
	 * 
	 * @return void
	 */
	public function dispatch()
	{
		if ($this->app->routesAreCached()) {
			$this->builder->extractCachedRoutes();
		} else {
			$this->builder->resolve();
			$this->builder->resolve('middlewares');
			$this->builder->cacheRoutes();
		}

		$this->pipeline->send(
			$this->request, 
			$this->createMiddlewareStageCallback(), 
			$this->createControllerStageCallback()
		);
	}

	/**
	 * Creates callback for next stage when middlewrae route is empty
	 *
	 * @return Closure The callback
	 */
	private function createNullStageCallback()
	{
		return function() {
			$middleware = array();
			$middleware[] = function($input, $next) {
				return $next($input);
			};

			return $middleware;
		};
	}

	/**
	 * Handle current requested route
	 * 
	 * @param array $routes
	 *
	 * @return array
	 */	
	private function handleRequest(array $routes)
	{
		// Counter to keep track of the number of routes we've handled
		$indicator = 0;
		$routes = $routes[Route::TARGET_NAME];
		// The current page URL
		$uri = $this->builder->getRequestUri();
		$uri = urldecode(urldecode($uri));
		
		foreach ($routes as &$handlers) {
			foreach ($handlers as $target => &$handler) {
				$pattern = '#^' . $target . '$#';
				if (preg_match_all($pattern, $uri, $matches, PREG_SET_ORDER)) {
					$this->resolveReflectionController($target, $handler);

					// Extract the matched URL parameters (and only the parameters)
					$primitives = array_map(function($match) {
						$var = explode('/', trim($match, '/'));
						return isset($var[0]) ? $var[0] : null;
					}, array_slice($matches[0], 1));
						
					$indicator++;
						
					return $this->resolve(
						$indicator, 
						$target, 
						$handler, 
						$primitives
					);
				}
			}
		}
	}

	/**
	 * Creates callback for middlewrae route stage
	 *
	 * @return array The callback list
	 */	
	private function createMiddlewareStageCallback()
	{
		//Request method
		$requestedMethod = $this->builder->getRequestMethod();

		//If the middlewares route is sent by current HTTP Request method
		if (isset($this->builder->middlewares[$requestedMethod])) {
			$middlewares = $this->handleRequestFromEmptyContext();
			
			if (isset($this->builder->middlewares[$requestedMethod])) {
				//Grab middlewares route by http request method
				$middlewares = $this->builder->middlewares[$requestedMethod];
				//Filter request based on current request
				$middlewares = $this->handleRequest($middlewares);
			}
			//Indicator is an tracker to tell us whether or not requested route exist.
			list($indicator, $middlewares, $params) = $middlewares;

			if (is_array($middlewares)) {
				extract($middlewares);
				$middleware = $handler;
			}

			//If the given handler/callback middleware is a string format
			//It's mean class name,
			//So we'll resolve that class name through  
			if (isset($middleware) && is_string($middleware)) {
				$middleware = $this->app->make($middleware);
			}

			//If the given handler is null
			//We need create null middleware stage
			//So the pipeline middleware won't throw us an exception
			if (is_null($middlewares)) {
				$middleware = $this->createNullStageCallback()();
			}

			//Lastly, if the resolved middleware handler not an array
			//We should wrap it up as array(It is a requirement of pipeline)
			(is_array($middleware))?
				$middleware = $middleware:
				$middleware = array($middleware);
			
			$middleware = Collection::make($middleware)->map(function($middleware) {
				if (is_string($middleware)) {
					$middleware = $this->app->make($middleware);
				} else {
					$middleware = $middleware;
				}
				
				return $middleware;
			});

			return $middleware->all();
		} else {
			return $this->createNullStageCallback()();
		}
	}
	
	/**
	 * Creates callback for regular route stage
	 *
	 * @return \Closure The callback
	 */
	private function createControllerStageCallback()
	{
		return function (RequestInterface $request) {
			$indicator = 0;
			//Request method
			$requestedMethod = $this->builder->getRequestMethod();
			
			$builders = $this->handleRequestFromEmptyContext();

			if (isset($this->builder->routes[$requestedMethod])) {
				$routes = $this->builder->routes[$requestedMethod];
				$builders = $this->handleRequest($routes);
			}
			
			list($indicator, $handlers, $params) = $builders;

			if (!is_null($handlers)) {
				extract($handlers);
			}

			// If no route was handled, trigger the 404 (if any)
			if ($indicator === 0 || $indicator === null) {
				if ($requestedMethod === 'options') {
					throw new HttpException(204, 'No Content');
				}

				throw new NotFoundHttpException(__('PAGE_NOT_FOUND'));
			}

			$resolved = $this->resolveCallback(
				$indicator, 
				$handlers, 
				$params
			);

			list($indicator, $response) = $resolved;

			return $response;
		};
	}
	
	/**
	 * Handle request when the called request route is empty
	 *
	 * @return array
	 */	
	private function handleRequestFromEmptyContext()
	{
		return $this->resolve(0, '', '', array());
	}
	
	/**
	 * Resolve matched regular routes
	 * 
	 * @param int $indicator
	 * @param string $target
	 * @param string $handler
	 * @param array $primitives
	 *
	 * @return array
	 */	
	private function resolve($indicator, $target, $handler, $primitives)
	{
		$collection = Collection::make(array());
		$collection->add(Route::TARGET_NAME, $target);
		$collection->add(Route::HANDLER_NAME, $handler);
		
		return array($indicator, $collection->all(), $primitives);
	}

	/**
	 * Call callback handler for the current requested route
	 * by the given primitves parameters
	 * 
	 * @param array $routes
	 *
	 * @return array
	 */	
	private function resolveCallback($indicator, $routes, $primitives)
	{
		$indicator = $indicator;

		$callback = $routes[Route::HANDLER_NAME];

		$handler = $callback instanceof Closure;

		if ($handler) {
			$reflector = new \ReflectionFunction($callback);
			$this->controller->registerApplication($this->app);
			$response = $this->controller->resolveControllerHandler($reflector, $callback, $primitives);
		} else {
			if(!$handler && $callback !== null) {
				$response = $this->resolveMethod($routes, $primitives);
			}

			$response = null;
		}

		return array($indicator, $response);
	}

	/**
	 * Call controller handler for the current requested route
	 * by the given primitves parameters
	 * 
	 * @param array $routes
	 *
	 * @return array
	 */	
	private function resolveMethod($routes, $primitives)
	{
		$controller = $this->controller;
		$callback = $routes['handler'];
		$callback = Str::parseCallback($callback, Controller::DEFAULT_METHOD);

		$controller->registerApplication($this->app);

		$reflector = new ReflectionMethod($callback[0], $callback[1]);

		$controller->resolveControllerClass(
			$callback[0], 
			$callback[0]
		);

		$controller->resolveControllerHandler(
			$reflector, 
			$callback[0], 
			$primitives
		);
	}

	/**
	 * Resolve controller reflection
	 * 
	 * @param array $routes
	 * @param array $primitives
	 *
	 * @return void
	 */
	public function resolveReflectionController($target, $handler)
	{
		$callback = $handler;

		if (!$handler instanceof Closure && is_string($handler)) {
			$callback = Str::parseCallback($handler, Controller::DEFAULT_METHOD);
			
			if (method_exists($callback[0], 'beforeMount')) {
				$callback[0]::beforeMount();
			}
		}

		if ($this->builder->hasAlias($target)) {
			$alias = $this->builder->getAlias($target);
			$this->controller->setTarget($alias, $target);
			
			switch ($callback) {
				case $callback instanceof Closure:
					$this->controller->setClassName($alias, $callback);
					;break;
				default:
					$this->controller->setClassName($alias, $callback[0]);
					$this->controller->setMethodName($alias, $callback[1]);
					;break;
			}
		}
	}
}