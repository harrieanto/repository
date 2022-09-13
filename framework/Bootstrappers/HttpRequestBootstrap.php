<?php
namespace Repository\Component\Bootstrappers;

/**
 * HTTP Request Bootstrapper.
 *
 * @package	  \Repository\Component\Bootstrap
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class HttpRequestBootstrap extends Bootstrap
{
	/**
	 * @{inheritdoc}
	 * See \Repository\Component\Contracts\Bootstrap\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
        $request = $this->app['request'];
        $servers = $request->getServerParams();
        
        if (isset($servers['REMOTE_ADDR'])) {
            $ip = $servers['REMOTE_ADDR'];
            $request->setTrustedProxies(array($ip));
        }
	}
}