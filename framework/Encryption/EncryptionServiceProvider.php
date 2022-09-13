<?php
namespace Repository\Component\Encryption;

use Repository\Component\Support\ServiceProvider;
use Repository\Component\Contracts\Encryption\EncryptionInterface;

/**
 * Encryption Service Provider.
 *
 * @package	  \Repository\Component\Encryption
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class EncryptionServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->registerEncryption();
		$this->registerEncryptionContracts();
	}
	
	private function registerEncryption()
	{
		$this->app->bind('encryption', function ($app) {
			$encryption = new Manager($app);
			return $encryption->driver();
		});
	}
	
	private function registerEncryptionContracts()
	{
		$this->app->bind(EncryptionInterface::class, function ($app) {
			return $app['encryption'];
		});
	}
}