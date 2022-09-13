<?php
namespace Repository\Component\Hashing;

use Repository\Component\Support\ServiceProvider;

/**
 * Hash Service Provider.
 *
 * @package	  \Repository\Component\Hashing
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class HashServiceProvider extends ServiceProvider
{
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Support\ServiceProvider::register()
	 */
	public function register()
	{
		$this->app->bind('hash', function() {
			return new Hash();
		});
	}
}