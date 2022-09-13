<?php
namespace Repository\Component\Contracts\Console;

/**
 * Console Response Interface.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface ResponseInterface
{
	/**
	 * Set plain response message
	 * 
	 * @param string $message
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	public function plain(string $message, $includeNewline = false);

	/**
	 * Set info response message
	 * 
	 * @param string $message
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	public function info(string $message, $includeNewline = false);

	/**
	 * Set comment response message
	 * 
	 * @param string $message
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	public function comment(string $message, $includeNewline = false);

	/**
	 * Set warning response message
	 * 
	 * @param string $message
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	public function warning(string $message, $includeNewline = false);

	/**
	 * Set error response message
	 * 
	 * @param string $message
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	public function error(string $message, $includeNewline = false);

	/**
	 * Write response message to the resource
	 * 
	 * @param string|array $messages
	 * @param bool Determine whether or not response message will include newline
	 * 
	 * @return void
	 */
	public function write($messages, $includeNewline = false);

	/**
	 * Write response message to the resource with newline include in the end of message
	 * 
	 * @param string|array $messages
	 * 
	 * @return void
	 */
	public function writeln($messages);

	/**
	 * Clear rendered response message
	 * 
	 * @return void
	 */
	public function clear();
}