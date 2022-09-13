<?php
namespace Repository\Component\Http\Exception;

/**
 * Hanlde Not Found HTTP Exception.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class NotFoundHttpException extends HttpException
{
	/**
	 * @param string $message  The internal exception message
	 * @param \Exception $previous The previous exception
	 * 
	 * @param int $code The internal exception code
	 */
	public function __construct($message = null,  \Exception $previous = null,  $code = 0)
	{
		parent::__construct(404,  $message,  $previous,  array(),  $code);
	}
}