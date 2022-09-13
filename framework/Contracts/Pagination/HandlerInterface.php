<?php
namespace Repository\Component\Contracts\Pagination;

use Repository\Component\Pagination\PaginationParameter;

/**
 * Handle pagination logic.
 *
 * @package	  \Repository\Component\Pagination
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface HandlerInterface
{
	/**
	 * Handle pagination logic
	 * 
	 * @param \Repository\Component\Pagination\PaginationParameter
	 * 
	 * @return void
	 */
	public function handle(PaginationParameter $pagination): void;
}