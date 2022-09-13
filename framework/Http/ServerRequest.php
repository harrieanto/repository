<?php
namespace Repository\Component\Http;

use Repository\Component\Support\Str;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * PSR-7 In-Bound Request.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ServerRequest extends ClientRequest implements ServerRequestInterface
{
	/**
	 * $_SERVER list
	 * @var array $serverParams
	 */
	protected $serverParams;

	/**
	 * $_COOKIE list
	 * @var array $cookies
	 */
	protected $cookies;

	/**
	 * Content-Type Header
	 * @var string $contentType
	 */
	protected $contentType;

	/**
	 * $_GET list
	 * @var array $queryParams
	 */
	protected $queryParams;

	/**
	 * Request message parsed body
	 * @var mixed $parsedBody
	 */
	protected $parsedBody;

	/**
	 * Additional attribute
	 * 
	 * @var array $attributes
	 */
	protected $attributes;

	/**
	 * Request Method
	 * @var string $method
	 */
	protected $method;

	/**
	 * $_FILES list
	 * @var array $uploadedFileInfo
	 */
	protected $uploadedFileInfo;

	/**
	 * UploadedFile instance
	 * @var \Psr\Request\Message\UploadedFileInterface $uploadedFile
	 */
	protected $uploadedFile;
	
	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getServerParams()
	 */
	public function getServerParams()
	{
		if (!$this->serverParams) {
			$this->serverParams = $_SERVER;
		}

		return $this->serverParams;
	}

	public function withServerParams(array $params)
	{
		$this->serverParams = array_merge($this->getServerParams(), $params);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getCookieParams()
	 */
	public function getCookieParams()
	{
		if (!$this->cookies) {
			$this->cookies = $_COOKIE;
		}

		return $this->cookies;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getQueryParams()
	 */
	public function getQueryParams()
	{
		if (!$this->queryParams) {
			$this->queryParams = $_GET;
		}
		
		return $this->queryParams;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getUploadedFileInfo()
	 */
	public function getUploadedFileInfo()
	{
		if (!$this->serverParams) {
			$this->uploadedFileInfo = $_FILES;
		}
		return $this->uploadedFileInfo;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getRequestMethod()
	 */
	public function getRequestMethod()
	{
		$method = $this->getServerParams()['REQUEST_METHOD'] ?? '';

		$this->method = strtolower($method);

		return $this->method;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getContentType()
	 */
	public function getContentType()
	{
		if (!$this->contentType) {
			$this->contentType = $this->getServerParams()['CONTENT_TYPE'] ?? '';
			$this->contentType = strtolower($this->contentType);
		}

		return $this->contentType;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getUploadedFile()
	 */
	public function getUploadedFiles()
	{
		if (!is_null($this->uploadedFile)) {
			foreach ($this->getUploadedFileInfo() as $field => $value) {
				$this->uploadedFile[$field] = new UploadedFile($field, $value);
			}
		}
		return $this->uploadedFile;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::withCookieParams()
	 */
	public function withCookieParams(array $cookies)
	{
		$this->cookies = array_merge($this->getCookieParams(), $cookies);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::withQueryParams()
	 */
	public function withQueryParams(array $query)
	{
		$this->queryParams = array_merge($this->getQueryParams(), $query);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::withUploadedFile()
	 */
	public function withUploadedFiles(array $uploadedFiles)
	{
		if (!count($uploadedFiles)) {
			throw new InvalidArgumentException(
				"No Uploaded File was Added"
			);
		}
		
		foreach ($uploadedFiles as $uploadedFile) {
			if (!$uploadedFile instanceof UploadedFileInterface) {
				throw new InvalidArgumentException(
					"Uploaded File must be Instance of ".
					UploadedFileInterface::class
				);
			}
		}
		
		$this->uploadedFile = $uploadedFiles;
		
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getParsedBody()
	 */
	public function getParsedBody()
	{
		$contentType = $this->getContentType();
		$contentType = explode(';', $contentType);
		$requestMethod = Str::upper($this->getRequestMethod());

		$form = (
			in_array(Request::FORM_ENCODED, $contentType) || 
			in_array(Request::MULTI_FORM, $contentType) &&
			$requestMethod === mb_strtoupper(Request::POST)
		);
		
		$json = (
			in_array(Response::CONTENT_TYPE_JSON, $contentType) ||
			in_array(Response::CONTENT_TYPE_HAL_JSON, $contentType) &&
			$requestMethod === mb_strtoupper(Request::POST)
		);
		
		if ($this->parsedBody) {
			return $this->parsedBody;
		}

		if ($form) {
			$this->parsedBody = $_POST;
		} else if ($json) {
			$this->parsedBody = json_decode(file_get_contents('php://input'), true);
		} elseif (!empty($_REQUEST)) {
			$this->parsedBody = $_REQUEST;
		} else {
			ini_set("allow_url_fopen", true);
			$this->parsedBody = file_get_contents('php://input');
		}

		return $this->parsedBody;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::withParsedBody()
	 */
	public function withParsedBody($data)
	{
		$this->parsedBody = $data;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getAttributes()
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::getAttribute()
	 */
	public function getAttribute($name, $default = null)
	{
		return $this->attributes[$name] ?? $default;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::withAttribute()
	 */
	public function withAttribute($name, $value)
	{
		$this->attributes[$name] = $value;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\ServerRequest::withoutAttribute()
	 */
	public function withoutAttribute($name)
	{
		if (isset($this->attributes[$name])) {
			unset($this->attributes[$name]);
		}

		return $this;
	}

	/**
	 * Initialize Request parameter lists
	 * @return \Psr\Http\Message\ServerRequest
	 */
	public function initialize()
	{
		$this->getServerParams();
		$this->getCookieParams();
		$this->getQueryParams();
		$this->getUploadedFiles();
		$this->getRequestMethod();
		$this->getContentType();
		$this->getParsedBody();
		return $this;
	}
}