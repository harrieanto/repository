<?php
namespace Repository\Component\Session;

use SessionHandlerInterface;
use Repository\Component\Http\Cookie;
use Repository\Component\Support\ServiceProvider;
use Repository\Component\Session\Handlers\Manager;
use Repository\Component\Contracts\Session\SessionInterface;

/**
 * Session Service Provider.
 * 
 * @package	  \Repository\Component\Session
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class SessionServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->registerSessionCookie();
		$this->registerSessionTransaction();
		$this->registerSessionFlash();
		$this->registerSessionHandler();
		$this->registerSessionConcreteBindings();
		$this->registerSessionContracts();
		$this->registerSessionFactory();
	}
	
	private function registerSessionCookie()
	{
		$this->app->singleton('session.cookie', function ($app) {
			$cookie = new CookieParameter($app['response'], new Cookie);
			return $cookie;
		});
	}
	
	private function registerSessionTransaction()
	{
		$this->app->singleton('session', function ($app) {
			$cookies = $app['request']->getCookieParams();
			$sessions = $app['config']['session'];

			$id = new IdGenerator;
			$id = $id->generate();
			
			if (!isset($cookies[$sessions['cookie_name']])) {
				$app['session.cookie']->setCookie($id);
				return new Session($id, $sessions['cookie_name']);
			} else {
				$cookieId = $cookies[$sessions['cookie_name']];

				return new Session($cookieId, $sessions['cookie_name']);
			}
		});
	}

	private function registerSessionFlash()
	{
		$this->app->singleton('flash', function ($app) {
			return new Flash($app['session']);
		});
	}
	
	private function registerSessionHandler()
	{
		$this->app->singleton('session.handler', function ($app) {

			$handler = new Manager($app);
			
			return $handler->driver();
		});
	}
	
	private function registerSessionConcreteBindings()
	{
		$this->app->singleton(Session::class, function ($app) {
			return $app['session'];
		});

		$this->app->singleton(Flash::class, function ($app) {
			return $app['flash'];
		});
	}
	
	private function registerSessionContracts()
	{
		$this->app->singleton(SessionInterface::class, function ($app) {
			return $app['session'];
		});

		$this->app->singleton(SessionHandlerInterface::class, function ($app) {
			return $app['session.handler'];
		});
	}
	
	private function registerSessionFactory()
	{
		$this->app->singleton(SessionFactory::class, function ($app) {
			$session = new SessionFactory(
				$app, 
				$app['session'], 
				$app['session.handler']
			);
			
			return $session;
		});
	}
}