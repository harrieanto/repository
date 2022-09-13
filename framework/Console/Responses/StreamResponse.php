<?php
namespace Repository\Component\Console\Responses;

use InvalidArgumentException;
use Repository\Component\Contracts\Console\OutputFormatterInterface;

/**
 * Handle Stream Response.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class StreamResponse extends Response
{
	/**
	 * The stream resource to write to
	 * @var resource $stream
	 */
	private $stream;

	/**
	 * @param resource $stream The stream to write to
	 * @param Repository\Component\Contracts\Console\OutputFormatterInterface $formatter
	 */
	public function __construct($stream, OutputFormatterInterface $formatter)
	{
		if (!is_resource($stream)) {
			throw new InvalidArgumentException("The given input stream isn't resource");
		}
		
		parent::__construct($formatter);
		
		$this->stream = $stream;
	}

	/**
	 * Get defined stream resource
	 * 
	 * @return resource
	 */	
	public function getStream()
	{
		return $this->stream;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Console\Responses\Response::doWrite()
	 */	
	protected function doWrite(string $message, $includeNewline = false)
	{
		$includeNewline = $includeNewline ? PHP_EOL : '';

		fwrite($this->stream, $message . $includeNewline);
		fflush($this->stream);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Console\Responses\Response::clear()
	 */
	public function clear()
	{
		// Don't do anything
		// Let another child class define this
	}
}