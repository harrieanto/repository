<?php
namespace Repository\Component\Contracts\Pagination;

/**
 * Pagination.
 *
 * @package	  \Repository\Component\Pagination
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface PaginationInterface
{	
	/**
	 * Set total page by the given list items
	 * 
	 * @return \Repository\Component\Pagination\PageCollection
	 */
	public function resolveTotalPage();

	/**
	 * Handle not found page of the items list
	 *
	 * @param string $message
	 *
	 * @return \Repository\Component\Pagination\PageCollection
	 */
	public function resolveNotFound($message = 'Page Not Found');

	/**
	 * Handle pagination logic
	 * 
	 * @return void
	 */
	public function handle(): void;
}