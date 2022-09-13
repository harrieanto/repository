<?php
namespace Repository\Component\Console\Responses;

use Repository\Component\Contracts\Console\ResponseInterface;

/**
 * Handle Cursor Console.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class CursorConsole
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
	 * Restore cursor position
	 *  
	 * @return void
	 */	
	public function restore()
	{
		$this->response->write("\0338");
	}

	/**
	 * Save cursor position
	 *  
	 * @return void
	 */	
	public function save()
	{
		$this->response->write("\0337");
	}
}