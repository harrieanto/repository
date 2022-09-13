<?php
namespace Repository\Component\Http\Exception;

/**
 * The HTTP Error Exception Interface.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface HttpExceptionInterface
{
	/**
	 * Returns the status code
	 * 
	 * @return int An HTTP response status code
	 */
	public function getStatusCode();

	/**
	 * Returns the status text
	 * 
	 * @return string An HTTP response status text
	 */
	public function getStatusText();

	/**
	 * Returns response headers
	 *
	 * @return array Response headers
	 */
	public function getHeaders();
}