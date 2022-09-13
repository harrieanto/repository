<?php
namespace Repository\Component\Console\Responses;

use Repository\Component\Contracts\Console\OutputFormatterInterface;

/**
 * Handle Console Response.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ConsoleResponse extends StreamResponse
{
	/**
	 * Get cursor console instance
	 * 
	 * @return \Repository\Component\Console\Reponses\CursorConsole
	 */	
	 private $cursor;

	/**
	 * Get console eraser instance
	 * 
	 * @return \Repository\Component\Console\Reponses\ConsoleEraser
	 */	
	 private $eraser;

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Console\Responses\Response::__construct()
	 */
	public function __construct(OutputFormatterInterface $formatter, $stream = STDOUT)
	{
		parent::__construct($stream, $formatter);
		$this->cursor = new CursorConsole($this);
		$this->eraser = new ConsoleEraser($this);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Console\Responses\Response::clear()
	 */
	public function clear()
	{
		//First we need to restore the pointer position.
		$this->cursor->restore();
		//So when pointer is restored the pointer can sit down on top of the screen
		//And we can erase properly
		$this->eraser->clear();
	}

	/**
	 * Get console cursor instance
	 * 
	 * @return \Repository\Component\Console\Reponses\CursorConsole
	 */	
	public function getCursor()
	{
		return $this->cursor;
	}

	/**
	 * Get console eraser instance
	 * 
	 * @return \Repository\Component\Console\Reponses\ConsoleEraser
	 */	
	public function getEraser()
	{
		return $this->eraser;
	}

	/**
	 * Get the height size of the terminal/console screen
	 * 
	 * @return int
	 */
	public function getScreenHeight()
	{
		return intval(`tput lines`);
	}

	/**
	 * Get the width size of the terminal/console screen
	 * 
	 * @return int
	 */
	public function getScreenWidth()
	{
		return intval(`tput cols`);
	}
}