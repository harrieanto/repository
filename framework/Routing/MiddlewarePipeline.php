<?php
namespace Repository\Component\Routing;

use Psr\Http\Message\RequestInterface;
use Repository\Component\Http\Request;
use Repository\Component\Http\Response;
use Repository\Component\Pipeline\Pipeline;
use Repository\Component\Routing\Exception\RouteException;
use Repository\Component\Pipeline\Exception\PipelineException;

/**
 * Middleware Pipeline.
 * 
 * @package	  \Repository\Component\Routing
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class MiddlewarePipeline
{
	/**
	 * @inheritdoc
	 */
	public function send(RequestInterface $request, array $middleware, callable $controller)
	{
		try {
			$response = (new Pipeline)
				->send($request)
				->through($middleware, 'handle')
				->then($controller)
				->execute();

			return $response ?? new Response();
		} catch (PipelineException $ex) {
			throw new RouteException('Failed to send request through middleware pipeline', 0, $ex);
		}
	}
}
