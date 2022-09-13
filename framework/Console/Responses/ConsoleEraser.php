<?php
namespace Repository\Component\Console\Responses;

use Repository\Component\Contracts\Console\ResponseInterface;

/**
 * Erase Console Response.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ConsoleEraser
{
	/**
	 * The console response instance
	 * @var \Repository\Component\Contracts\Console\ResponseInterface $response
	 */
	private $response;

	/**
	 * @param \Repository\Component\Contracts\Console\ResponseInterface $response
	 */
	public function __construct(ResponseInterface $response)
	{
		$this->response = $response;
	}

	/**
	 * Erase entire console screen
	 *  
	 * @return void
	 */	
	public function clear(): void
	{
		$this->response->write("\033[2J");
	}

	/**
	 * Erase console screen only in the the current line
	 *  
	 * @return void
	 */	
	public function currentLine(): void
	{
		$this->response->write("\033[2K");
	}

	/**
	 * Erase console screen in the the current line up
	 *  
	 * @return void
	 */	
	public function currentLineUp(): void
	{
		$this->response->write("\033[1J");
	}

	/**
	 * Erase console screen in the the current line down
	 *  
	 * @return void
	 */	
	public function currentLineDown(): void
	{
		$this->response->write("\033[J");
	}

	/**
	 * Erase console screen and put pointer to the start line
	 *  
	 * @return void
	 */	
	public function toStartLine(): void
	{
		$this->response->write("\033[1K");
	}

	/**
	 * Erase console screen and put pointer to the end line
	 *  
	 * @return void
	 */	
	public function toEndLine(): void
	{
		$this->response->write("\033[K");
	}
}