<?php
namespace Repository\Component\Bootstrappers;

use Repository\Component\Foundation\Application;

/**
 * Application Bootstrapper Factory.
 *
 * @package	  \Repository\Component\Bootstrap
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class BootstrapFactory
{
	/**
	 * Bootstrap application
	 * @return \Repository\Component\Foundation\Application
	 */
	public function bootstrap($basePath = null)
	{
		$app = new Application($basePath);
		//Register any application bootstrappers
		//Be careful tinker the following order
		//The arbitrary order will break your app
		$bootstrappers = array(
			new Bootstrap($app), 
			new EnvBootstrap($app), 
			new FileRepositoryBootstrap($app), 
			new DebugBootstrap($app), 
			new InvokeStaticBootstrap($app), 
			new RouteBootstrap($app), 
			new AutoloadBootstrap($app), 
			new ServiceProviderBootstrap($app), 
			new LocalizationBootstrap($app), 
			new HttpRequestBootstrap($app), 
			new HttpKernelBootstrap($app)
		);
		
		foreach ($bootstrappers as $bootstrapper) {
			$bootstrapper->bootstrap();
		}
		
		return $app;
	}
}