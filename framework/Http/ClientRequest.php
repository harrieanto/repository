<?php
namespace Repository\Component\Http;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * PSR-7 Out-Bound Request.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ClientRequest extends Message implements RequestInterface
{
	/**
	 * Request target
	 * @var string $target
	 */
	 protected $target;

	/**
	 * UriInterface instance
	 * @var \psr\Http\Message\UriInterface $uri
	 */
	protected $uri;

	/**
	 * HTTP Method
	 * 
	 * @var string $method
	 */
	protected $method;
	
	/**
	 * @param string $target Request target
	 * @param string $method HTTP method request
	 * @param \Psr\Http\Message\STreamINterface $body HTTP body message
	 * @param array $headers Http headers list
	 * @param string $targetHTTP protocol version
	 */
	public function __construct(
		$target = null, 
		$method = null, 
		StreamInterface $body = null, 
		$headers = null, 
		$version = null)
	{
		$this->target = $target;
		$this->body = $body;
		$this->method = $this->checkMethod($method);
		$this->httpHeaders = $headers;
		$this->version = $this->onlyVersion($version);
	}
	
	/**
	 * Check HTTP method validity
	 * 
	 * @param string $method
	 * 
	 * @throw InvalidArgumentException
	 * 
	 * @return string
	 */
	protected function checkMethod($method)
	{
		if (!$method === null) {
			if (!in_array(strtolower($method), Request::$httpMethods)) {
				throw new InvalidArgumentException(
					"HTTP Method [$method] Not Allowed!"
				);
			}
		}

		return $method;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\RequestInterface::getRequestTarget()
	 */
	public function getRequestTarget()
	{
		return $this->target ?? Request::DEFAULT_REQUEST_TARGET;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\RequestInterface::withRequestTarget()
	 */
	public function withRequestTarget($requestTarget)
	{
		$this->target = $requestTarget;
		$this->getUri();
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\RequestInterface::getMethod()
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\RequestInterface::withMethod()
	 */
	public function withMethod($method)
	{
		$this->method = $this->checkMethod($method);
		return $this;
	}
	
	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\RequestInterface::getUri()
	 */
	public function getUri()
	{
		if (!$this->uri) {
			$this->uri = new Uri($this->target);
		}

		return $this->uri;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\RequestInterface::withUri()
	 */
	public function withUri(UriInterface $uri, $preserveHost = false)
	{
		if ($preserveHost) {
			$found = $this->findHeader(Request::HEADER_HOST);

			if (!$found && $uri->getHost()) {
				$this->httpHeaders[Request::HEADER_HOST] = $uri->getHost();
			}
		} elseif ($uri->getHost()) {
			$this->httpHeaders[Request::HEADER_HOST] = $uri->getHost();
		}
		
		$this->uri = $uri;
		return $this;
	}
}