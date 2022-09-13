<?php
namespace Repository\Component\Contracts\Http\Middleware;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Http Middleware Interface.
 * 
 * @package	 \Repository\Component\Contracts\Http
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface MiddlewareInterface
{
	public function handle(RequestInterface $request, Closure $next):? ResponseInterface;
}