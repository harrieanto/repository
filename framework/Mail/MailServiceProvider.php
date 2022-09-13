<?php
namespace Repository\Component\Mail;

use Repository\Component\Contracts\Mail\IMailConnection;
use Repository\Component\Support\ServiceProvider;
use Repository\Component\Mail\Sender\Socket;

/**
 * Logger Service Provider.
 *
 * @package	  \Repository\Component\Mail
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://monsterdashboard.id/hariyanto
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository-framework/LICENSE.md
 */
class MailServiceProvider extends ServiceProvider
{
	public function register()
	{
        $this->registerMailConnection();
		$this->app->bind('mailer', function($app) {
			return new Smtp($app[UriInterface::class], $app[IMailConnection::class]);
		});
    }
    
    private function registerMailConection()
    {
        $this->app->bind(IMailConnection::class, function ($app) {
            return new Socket(new Mailer($app));
        });
    }
}