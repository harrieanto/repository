<?php
namespace Repository\Component\Log;

use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Repository\Component\Collection\Collection;

/**
 * Stream Logger.
 *
 * @package	  \Repository\Component\Log
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class StreamLogger extends AbstractLogger implements LoggerInterface
{
	/** The interpolate helpers **/
	use InterpolateTrait;

	/** The logger channel type **/
	const CHANNEL_TYPE = "LOCAL_STREAM";
	
	/**
	 * Stream instance
	 * @var \Psr\Http\Message\StreamInterface $stream
	 */	
	private $stream;

	/**
	 * The path to save log message
	 * @var string $path
	 */		
	private $path;

	/**
	 * Log level list
	 * @var array $levels
	 **/
	private static $levels = array(
		LogLevel::EMERGENCY => 7, 
		LogLevel::ALERT => 6, 
		LogLevel::CRITICAL => 5, 
		LogLevel::ERROR => 4, 
		LogLevel::WARNING => 3, 
		LogLevel::NOTICE => 2, 
		LogLevel::INFO => 1, 
		LogLevel::DEBUG => 0, 
	);

	/**
	 * @param \Psr\Http\Message\StreamInterface $stream
	 * @param string $path
	 */
	public function __construct(StreamInterface $stream, string $path)
	{
		$this->stream = $stream;
		$this->path = $path;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Log\LoggerInterface::log()
	 */
	public function log($level, $message, array $context = array())
	{
		$levels = Collection::make(static::$levels);
		$ex = "Logging with [$level] level isn't supported";

		if (is_string($level)) {
			if (!$levels->has($level))
				throw new InvalidArgumentException($ex);
		} else {
			if (!$levels->contains($level))
				throw new InvalidArgumentException($ex);
		}

		if (count($context) > 0) {
			$message = $this->interpolate($message, $context);
		}
		
		$time = new \DateTime('now');
		$time = $time->format("Y-m-d h:i:s");
		
		$messageCollection =  Collection::make(array());
		$messageCollection->add('channel', self::CHANNEL_TYPE);
		$messageCollection->add('created_at', $time);
		$messageCollection->add('message', $message);
		$messageCollection = $this->toJson($messageCollection->all());

		$this->stream->append($this->path, $messageCollection."\n\n");
	}

	/**
	 * Convert to json
	 * 
	 * @param string|array $content
	 * 
	 * @return string
	 */	
	private function toJson($content)
	{
		$encoded = json_encode($content, JSON_UNESCAPED_SLASHES || JSON_UNESCAPED_UNICODE);
		
		return $encoded;
	}

	/**
	 * Get log levels
	 * 
	 * @return array
	 */	
	public function getLevels()
	{
		$levels = static::$levels;
		
		return $levels;
	}
}