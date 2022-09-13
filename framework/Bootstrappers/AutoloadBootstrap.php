<?php
namespace Repository\Component\Bootstrappers;

use Repository\Component\Bootstrappers\Autoloaders\Autoload;

/**
 * Autoload Bootstrapper.
 *
 * @package	  \Repository\Component\Bootstrap
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class AutoloadBootstrap extends Bootstrap
{	
	/**
	 * @param \Repository\Component\Foundation\Application $app
	 */	
	public function bootstrap()
	{
		$this->app->instance('autoload', new Autoload(ROOT_PATH));
	}
}