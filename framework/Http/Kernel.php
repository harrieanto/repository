<?php
namespace Repository\Component\Http;

use Closure;
use Exception;
use Throwable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Repository\Component\Http\Response;
use Repository\Component\Pipeline\Pipeline;
use Repository\Component\Http\Exception\HttpException;
use Repository\Component\Contracts\Debug\ExceptionInterface;
use Repository\Component\Contracts\Container\ContainerInterface;
use Repository\Component\Contracts\Http\Middleware\MiddlewareInterface;

 /**
 * Http kernel
 * This class will handle requested request
 * In the same time will validate the request through defined global middleware list
 * before requested request compromised accessing our actual application
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Kernel
{
	/** The default handler method executed by pipeline **/
	const HANDLER_METHOD = 'handle';

	/**
	 * Container instance
	 * @var \Repository\Component\Contracts\Container\ContainerInterface $app
	 */
	protected $app;

	/**
	 * Global middlewares
	 * @var array $middlewares
	 */
	protected $middlewares = array();
	
	/**
	 * Excepted middlewares by the specific route
	 * @var array $exceptedMiddlewares
	 */
	protected $exceptedMiddlewares = array();
	
	/**
	 * Enabled middlewares
	 * @var array $enabledMiddlewares
	 */
	protected $enabledMiddlewares = array();

	/**
	 * Disabled middlewares
	 * @var array $disabledMiddlewares
	 */
	protected $disabledMiddlewares = array();
	
	/**
	 * Disabled all middleware identifier
	 * @var bool $disabledMiddleware
	 */
	protected $disabledMiddleware = false;

	/**
	 * Exception handler instance
	 * @var \Repository\Component\Contracts\Debug\ExceptionInterface $exceptionHandler
	 */
	private $exceptionHandler;

	/**
	 * @param \Repository\Component\Contracts\Container\ContainerInterface $app
	 * @param \Repository\Component\Contracts\Debug\ExceptionInterface $handler
	 */
	public function __construct(ContainerInterface $app, ExceptionInterface $handler)
	{
		$this->app = $app;
		$this->exceptionHandler = $handler;
	}

	/**
	 * Resolve excepted middleware on specific routes
	 * 
	 * @return void
	 */	
	public function middlewareExcepts()
	{
		$current = $this->app['request']->getCurrentUri();

		foreach ($this->exceptedMiddlewares as $path => $values) {
			$excepts = (array) $this->exceptedMiddlewares[$path];

			if (preg_match_all("/^{$path}/i", $current, $matches)) {
				foreach ($excepts as $except) {
					if (in_array($except, $this->middlewares)) {
						$this->middlewares = array_flip($this->middlewares);
						unset($this->middlewares[$except]);
						$this->middlewares = array_keys($this->middlewares);
					}
				}
			}
		}
	}

	/**
	 * Disabled all middleware
	 * 
	 * @return void
	 */	
	public function disabledAllMiddleware()
	{
		$this->disabledMiddleware = true;
	}

	/**
	 * Determine if the middleware is disabled
	 * 
	 * @return bool true When disabled, false otherwise
	 */	
	public function isMiddlewareDisabled()
	{
		if ($this->disabledMiddleware)
			return true;
		
		return false;
	}

	/**
	 * Add new middleware to the middleware list
	 * 
	 * @param string|array|object $middleware
	 * 
	 * @return void
	 */	
	public function addMiddleware($middleware)
	{
		if (!is_array($middleware))
			$middleware = [$middleware];
		
		$middleware = array_merge($this->middlewares, $middleware);
		
		$this->middlewares = $middleware;
	}

	/**
	 * Add middleware only to the the enabled middleware list
	 * 
	 * @param array $middleware
	 * 
	 * @return void
	 */	
	public function addOnlyEnableMiddleware(array $middlewares = array())
	{
		$this->enabledMiddlewares = $middlewares;
	}

	/**
	 * Add middleware only to the the disabled middleware list
	 * 
	 * @param array $middleware
	 * 
	 * @return void
	 */	
	public function addOnlyDisabledMiddleware(array $middlewares = array())
	{
		$this->disabledMiddlewares = $middlewares;
	}

	/**
	 * Handle inncoming request to the application
	 * 
	 * @param \Psr\Http\Message\RequestIntterface $request
	 * 
	 * @return \Psr\Http\Message\ResponseInterface
	 */	
	public function handle(RequestInterface $request): ResponseInterface
	{
		if (extension_loaded('zlib') && $request->hasHeader('Accept-Encoding')) {
			if (substr_count($request->getHeaderLine('Accept-Encoding'), 'gzip')) {
				ob_start('ob_gzhandler');
			}
		} else {
			ob_start();
		}

		if ($this->app->isDevMode()) {
			$this->app['route']->resolveRegisteredRoute();
		}
		
		try {
			$middlewares = $this->createMiddlewareStages($this->getMiddlewares());

			$response = (new Pipeline())
				->send($request)
				->through($middlewares, self::HANDLER_METHOD)
				->then(function ($request) {
					return $this->app->handle();
				})
				->execute();

			return $response ?? $this->app['response'];
		} catch (Exception $ex) {
			$this->exceptionHandler->handle($ex);
		} catch (Throwable $ex) {
			$this->exceptionHandler->handle($ex);
		}

		return $this->app['response'];
	}

	/**
	 * Create pipeline stage by the given middlewares list
	 * 
	 * @param array $middleware
	 * 
	 * @return array
	 */	
	public function createMiddlewareStages(array $middlewares)
	{
		$stages = array();
		$abstract = MiddlewareInterface::class;
		$ex = "Stage middleware should be instanceof [$abstract]";

		foreach ($middlewares as $middleware) {
			if (is_string($middleware)) {
				$middleware = $this->app->make($middleware);

				if (!$middleware instanceof MiddlewareInterface) {
					throw new \Exception($ex);
				}
				
				$stages[] = $middleware;
			} else {
				if (!$middleware instanceof MiddlewareInterface) {
					throw new \Exception($ex);
				}

				$stages[] = $middleware;
			}
		}
		
		return $stages;
	}

	/**
	 * Get global middlewares and enabled middlewares
	 * 
	 * @return array
	 */	
	public function getMiddlewares()
	{
		$this->middlewareExcepts();
		
		if ($this->disabledMiddleware) return [];
			
		if (count($this->enabledMiddlewares) > 0)
			return $this->enabledMiddlewares;

		if (count($this->disabledMiddlewares) > 0) {
			$enabledMiddlewares = array();
			
			foreach($this->middlewares as $middleware) {
				if (is_string($middleware)) {
					$middlewareClass = $middleware;
				} else {
					$middlewareClass = get_class($middleware);
				}
				
				if (!in_array($middlewareClass, $this->disabledMiddlewares)) {
					$enabledMiddlewares[] = $middleware;
				}
			}
			
			return $enabledMiddlewares;
		}

		return $this->middlewares;
	}
}