<?php
namespace Repository\Component\Pagination\Handlers;

use Repository\Component\Pagination\PaginationParameter;
use Repository\Component\Contracts\Pagination\HandlerInterface;

/**
 * Handle Pagination Logic over Array Collection.
 *
 * @package	  \Repository\Component\Pagination
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ArrayCollection implements HandlerInterface
{
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Pagination\HandlerInterface::handle()
	 */
	public function handle(PaginationParameter $pagination): void
	{
		$limit = $pagination->getLimit();
		$offset = $pagination->getTotalRow();
		$collection = $pagination->getArrayCollection();
		$collection = $collection->slice($offset, $limit);
		
		$pagination->setPageItems($collection);
	}
}