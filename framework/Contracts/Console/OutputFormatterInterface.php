<?php
namespace Repository\Component\Contracts\Console;

/**
 * Output formatting interface.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link l     https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface OutputFormatterInterface
{
	/**
	 * Set output formatting to the given string
	 * 
	 * @param string $message The string that want formatted
	 * @param \Closure|null $formatter This closure accept 1 paramater that holding a reference to this concrete class
	 * 
	 * @return string If the formatter not set and then this method should be return original string that was given
	 */
	public function setStringFormatting(string &$message, \Closure $formatter = null);
}