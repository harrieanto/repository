<?php
namespace Repository\Component\Bootstrappers;

use Repository\Component\Support\Statics\InvokeStatic;

/**
 * Application Component Invoked as Static Bootstrapper.
 *
 * @package	  \Repository\Component\Bootstrap
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class InvokeStaticBootstrap extends Bootstrap
{
	/**
	 * @{inheritdoc}
	 * See \Repository\Component\Contracts\Bootstrap\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
		InvokeStatic::clearResolvedInstances();
		InvokeStatic::setStaticApplication($this->app);
	}
}