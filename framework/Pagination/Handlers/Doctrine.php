<?php
namespace Repository\Component\Pagination\Handlers;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Repository\Component\Pagination\PaginationParameter;
use Repository\Component\Contracts\Pagination\HandlerInterface;

/**
 * Handle Pagination Logic over Doctrine DQL.
 *
 * @package	  \Repository\Component\Pagination
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Doctrine implements HandlerInterface
{
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Pagination\HandlerInterface::handle()
	 */
	public function handle(PaginationParameter $pagination): void
	{
		$offset = $pagination->getOffsetListPerPage();
		$totalPerPage = $pagination->getTotalListPerPage();

		if ($totalPerPage < 1) {
			$totalPerPage = 1;
		}

		$query = $pagination->getQueryInstance();
		
		$paginator = new Paginator($query->getDefaultQuery());

		if ($pagination->getTotalListPerPage() === 0) {
			$pagination->setTotalListPerPage(count($paginator));
		}

		$paginator->getQuery()
			->setFirstResult($offset)
			->setMaxResults($totalPerPage);
		
		$query->setDefaultQuery($paginator);
		
		$pagination->setPageItems($paginator->getIterator());
	}
}