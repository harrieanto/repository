<?php
namespace Repository\Component\Http\Middlewares\Exception;

use Repository\Component\Http\Response;
use Repository\Component\Http\Exception\HttpException;

/**
 * Post Too Large Exception.
 * 
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class PostTooLargeException extends HttpException
{
	public function __construct($message = null, \Exception $previous = null, array $headers = array(), $code = 0)
	{
		$message = !is_null($message) ? $message : Response::$statusCodes[413];
		parent::__construct(413, $message, $previous, $headers, $code);
	}
}