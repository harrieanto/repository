<?php
namespace Repository\Component\Http\Exception;

/**
 * Http Exception.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class HttpException extends \RuntimeException implements HttpExceptionInterface
{
	/**
	 * Http status code
	 * @var int $statusCode
	 */
	private $statusCode;

	/**
	 * Http status text
	 * @var int $statusText
	 */	
	private $statusText;

	/**
	 * Http headers
	 * @var aray $headers
	 */
	private $headers;

	public function __construct($statusCode, $message = null, \Exception $previous = null, array $headers = array(), $code = 0)
	{
		$this->statusCode = $statusCode;
		$this->statusText = $message;
		$this->headers = $headers;

		parent::__construct($message, $code, $previous);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Http\Exception\HttpExceptionInterface::getStatusCode()
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}
	
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Http\Exception\HttpExceptionInterface::getStatusText()
	 */
	public function getStatusText()
	{
		return $this->statusText;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Http\Exception\HttpExceptionInterface::getHeaders()
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Set response headers.
	 *
	 * @param array $headers Response headers
	 */
	public function setHeaders(array $headers)
	{
		$this->headers = $headers;
	}
}