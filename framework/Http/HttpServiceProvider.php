<?php
namespace Repository\Component\Http;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use Repository\Component\Support\ServiceProvider;

/**
 * HTTP Service Provider.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class HttpServiceProvider extends ServiceProvider
{
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Support\ServiceProvider
	 */
	public function register()
	{
		$this->registerUri();
		$this->registerHttpRequest();
		$this->registerHttpResponse();
		$this->registerJsonResponse();
	}
	
	public function registerHttpRequest()
	{
		$this->app->singleton('request', function($app) {
			return new Request();
		});
	}

	public function registerHttpResponse()
	{
		$this->app->singleton('response', function($app) {
			return new Response();
		});

		$this->app->singleton(ResponseInterface::class, function($app) {
			return $app['response'];
		});
	}

	public function registerJsonResponse()
	{
		$this->app->singleton('json.response', function($app) {
			return new JsonResponse();
		});
	}

	public function registerUri()
	{
		$this->app->singleton('uri', function($app) {
			return new Uri();
		});

		$this->app->bind(UriInterface::class, function($app) {
			return new Uri();
		});
	}
}