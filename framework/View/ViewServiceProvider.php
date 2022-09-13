<?php
namespace Repository\Component\View;

use App\Http\Services\View\View;
use Repository\Component\View\ViewFactory;
use Repository\Component\Support\ServiceProvider;
use Repository\Component\View\Compiler\CompilerFactory;

/**
 * View Service Provider.
 * 
 * @package	  \Repository\Component\View
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ViewServiceProvider extends ServiceProvider
{
	/**
	 * 
	 * Register the service provider.
	 *
	 * @return void
	 * 
	 */
	public function register()
	{
		$this->app->singleton('view.compiler', function ($app) {
			return new CompilerFactory($app['fs']);
		});

		$this->app->singleton(View::class, function ($app) {
			$view = new View($app['fs'], $app['view.compiler']);
			$view->registerApp($app);
			$view->registerShared();
			
			return $view;
		});

		$this->app->singleton('view', function ($app) {
			return $app[View::class];
		});
	}
}