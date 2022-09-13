<?php
namespace Repository\Component\Html;

use Repository\Component\Filesystem\Extension;
use Repository\Component\Support\ServiceProvider;

/**
 * Html Service Provider.
 *
 * @package	  \Repository\Component\Html
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class HtmlServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->singleton('html', function() {
			return new Html;
		});
	}
}