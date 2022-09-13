<?php
namespace Repository\Component\Console\Responses;

use Repository\Component\Contracts\Console\ResponseInterface;
use Repository\Component\Console\Responses\Formatter\Foreground;
use Repository\Component\Contracts\Console\OutputFormatterInterface;

/**
 * Abstract Response.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
abstract class Response implements ResponseInterface
{
	/**
	 * The output formatter iinstance
	 * @var Repository\Component\Contracts\Console\OutputFormatterInterface $formatter
	 */
	protected $formatter;

	/**
	 * @param Repository\Component\Contracts\Console\OutputFormatterInterface $formatter
	 */
	public function __construct(OutputFormatterInterface $formatter)
	{
		$this->formatter = $formatter;
	}

	/**
	 * Set plain response message
	 * 
	 * @param string $message
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	public function plain(string $message, $includeNewline = false)
	{
		$this->formatter->setStringFormatting($message);
		
		$this->write($message, $includeNewline);
	}

	/**
	 * Set info response message
	 * 
	 * @param string $message
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	public function info(string $message, $includeNewline = false)
	{
		$this->formatter->setStringFormatting($message, function($formatter) {
			$formatter->setTextColor(Foreground::GREEN);
		});
		
		$this->write($message, $includeNewline);
	}

	/**
	 * Set warning response message
	 * 
	 * @param string $message
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	public function warning(string $message, $includeNewline = false)
	{
		$this->formatter->setStringFormatting($message, function($formatter) {
			$formatter->setTextColor(Foreground::YELLOW);
		});
		
		$this->write($message, $includeNewline);
	}

	/**
	 * Set error response message
	 * 
	 * @param string $message
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	public function error(string $message, $includeNewline = false)
	{
		$this->formatter->setStringFormatting($message, function($formatter) {
			$formatter->setTextColor(Foreground::RED);
		});
		
		$this->write($message, $includeNewline);
	}

	/**
	 * Set comment response message
	 * 
	 * @param string $message
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	public function comment(string $message, $includeNewline= false)
	{
		$this->formatter->setStringFormatting($message, function($formatter) {
			$formatter->setTextColor(Foreground::LIGHT_GRAY);
		});
		
		$this->write($message, $includeNewline);
	}

	/**
	 * Write response message to the resource
	 * 
	 * @param string|array $messages
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	public function write($messages, $includeNewline = false)
	{
		if ($includeNewline) {
			$this->writeln($messages);
			return;
		}

		foreach ((array) $messages as $message) {
			$this->doWrite($message);
		}
	}

	/**
	 * Write response message to the resource with newline include in the end of message
	 * 
	 * @param string|array $messages
	 * 
	 * @return void
	 */
	public function writeln($messages)
	{
		foreach ((array) $messages as $message) {
			$this->doWrite($message, true);
		}
	}

	/**
	 * Clear rendered response message
	 * 
	 * @return void
	 */
	abstract public function clear();

	/**
	 * Write response message to the actual resource
	 * 
	 * @param string|array $messages
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	abstract protected function doWrite(string $message, $includeNewline = false);

	/**
	 * Get output formatter instance
	 * 
	 * @return \Repository\Component\Contracts\Console\OutputFormatterInterface
	 */	
	public function getOutputFormatter()
	{
		return $this->formatter;
	}
}