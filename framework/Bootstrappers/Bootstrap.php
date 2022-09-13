<?php
namespace Repository\Component\Bootstrappers;

use Repository\Component\Foundation\Application;
use Repository\Component\Contracts\Bootstrap\BootstrapInterface;

/**
 * Bootstrap.
 *
 * @package	  \Repository\Component\Bootstrap
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Bootstrap implements BootstrapInterface
{
	/**
	 * The appliciation instance
	 * @var \Repository\Component\Foundation\Application $app
	 */
	protected $app;

	/**
	 * @param \Repository\Component\Foundation\Application $app
	 */	
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * @{inheritdoc}
	 * See \Repository\Component\Contracts\Bootstrap\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
		//
	}
}