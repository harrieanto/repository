<?php
namespace Repository\Component\Bootstrappers;

use Repository\Component\Config\Config;
use Repository\Component\Config\EnvLoader;
use Repository\Component\Config\Repository;

/**
 * Application Environment Bootstrapper.
 *
 * @package	  \Repository\Component\Bootstrap
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class EnvBootstrap extends Bootstrap
{
	/**
	 * @{inheritdoc}
	 * See \Repository\Component\Contracts\Bootstrap\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
        if (is_file($this->app->getCachedConfigPath())) {
        	return;
        }

        $env = realpath(ROOT_PATH . '/.env');

        if (!file_exists($env)) {
        	throw new \Exception('Environment file not found!');
        }

		$loader = new EnvLoader($this->app, $env);
		$loader = new Repository($loader);
		
		Config::set('env', $loader);
		$this->app->instance('env', $loader);
	}
}