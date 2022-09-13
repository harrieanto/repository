<?php
namespace Repository\Component\Bootstrappers;

/**
 * App Localization Bootstrapper.
 *
 * @package	  \Repository\Component\Bootstrap
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class LocalizationBootstrap extends Bootstrap
{
	/**
	 * @{inheritdoc}
	 * See \Repository\Component\Contracts\Bootstrap\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
		date_default_timezone_set($this->app['config']['application']['timezone']);
	}
}