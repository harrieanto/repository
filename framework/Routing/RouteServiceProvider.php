<?php
namespace Repository\Component\Routing;

use Repository\Component\Support\ServiceProvider;
use Repository\Component\Routing\Controller\Controller;

/**
 * Route Service Provider.
 * 
 * @package	  \Repository\Component\Routing
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class RouteServiceProvider extends ServiceProvider
{
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Support\ServiceProvider::boot()
	 */
	public function boot()
	{
		$basepath = $this->app['config']['routes']['basepath'];
		$paths = ROOT_PATH . DS . $basepath . DS . '*/*';
		
		foreach (glob($paths) as $path) {
			$parts = explode(DS, $path);
			$path = implode(DS, array_slice($parts, 0, -1));
			
			if (is_dir($path) && is_readable($path)) {
				$this->app['route']->addPath($path, end($parts));
			}
		}
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Support\ServiceProvider::register()
	 */	
	public function register()
	{
		$this->registerBaseController();
		$this->registerMiddlewarePipeline();

		$this->app->singleton('route', function($app) {
			return new Route(
				$app, 
				$app['request'], 
				$app['response'], 
				$app['uri'], 
				$app['controller'], 
				$app['route.middleware']
			);
		});
	}

	/**
	 * Register middleware pipeline
	 * @return void
	 */	
	public function registerMiddlewarePipeline()
	{
		$this->app->singleton('route.middleware', function() {
			return new MiddlewarePipeline;
		});
	}

	/**
	 * Register application base controller
	 * @return void
	 */	
	public function registerBaseController()
	{
		$this->app->singleton('controller', function() {
			return new Controller;
		});
	}
}