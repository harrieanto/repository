<?php
namespace Repository\Component\Http;

use ArrayObject;
use InvalidArgumentException;
use Repository\Component\Http\TextStream;
use Repository\Component\Contracts\Http\Jsonable;

/**
 * Json Response.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class JsonResponse extends Response implements Jsonable
{
	/**
	 * @param mixed $content The content of the response
	 * @param int $statusCode The HTTP status code
	 * @param array $headers The headers to set
	 * @throws InvalidArgumentException Thrown if the content is not of the correct type
	 */
	public function __construct($content = [], int $statusCode = 200, array $headers = [])
	{
		$json = json_encode($content);
		$stream = new TextStream($json);

		parent::__construct($statusCode, $stream, $headers);
		
		$this->withHeader('Content-Type', Response::CONTENT_TYPE_JSON);
		$this->withHeader('Content-Length', $stream->getSize());
	}

	/**
	 * {@inheritdoc}
	 * 
	 * @throws InvalidArgumentException Thrown if the input could not be JSON encoded
	 */
	public function withBody($content)
	{
		if ($content instanceof ArrayObject) {
			$content = $content->getArrayCopy();
		}

		$json = json_encode($content);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new InvalidArgumentException('Failed to JSON encode content: ' . json_last_error_msg());
		}

		parent::withBody(new TextStream($json));
	}
	
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Http\Jsonable
	 */
	public function toJson()
	{
		return $this->getBody()->getContents();
	}
}
