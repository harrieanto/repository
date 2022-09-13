<?php
namespace Repository\Component\Pagination;

use Traversable;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;
use Repository\Component\Contracts\Pagination\HandlerInterface;
use Repository\Component\Contracts\Pagination\PaginationInterface;

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
class Pagination implements PaginationInterface
{
	/**
	 * Collection instance
	 * @var \Repository\Component\Collection\Collection $item
	 */
	protected $item;

	/**
	 * Pagination parameter
	 * @var \Repository\Component\Pagination\Paginate $parameter
	 */
	protected $parameter;

	/**
	 * The pagination handler
	 * @var \Repository\Component\Contracts\Pagination\HandlerInterface $handler
	 */
	protected $handler;

	/**
	 * Request instance
	 * @var \Psr\Http\Message\ServerRequestInterface $request
	 */	
	protected $request;

	/**
	 * Setup
	 * @param \Repository\Component\Pagination\PaginationParamater $parameter
	 * @param \Repository\Component\Contracts\Pagination\HandlerInterface $handler
	 * @param \Psr\Http\Message\ServerRequestInterface $request
	 */
	public function __construct(PaginationParameter $parameter, HandlerInterface $handler, ServerRequestInterface $request)
	{
		$this->parameter = $parameter;
		$this->handler = $handler;
		$this->request = $request;
	}

	/**
	 * Make collection to paginating
	 * 
	 * @param \Traversable $item
	 * 
	 * @return \Repository\Component\Pagination\AbstractPagination
	 */
	public function make(Traversable $item)
	{
		$this->item = $item;
		$this->parameter->setArrayCollection($item);
		
		return $this;
	}

	/**
	 * Set total row per page
	 * 
	 * @return void
	 */	
	public function resolveOffsetListPerPage(): void
	{
		$total = $this->parameter->getOffset()-PaginationParameter::INITIAL;

		$total = $total*$this->parameter->getTotalListPerPage();

		$this->parameter->setOffsetListPerPage($total);
	}
	
	/**
	 * Set total page by the given list items
	 * 
	 * @return void
	 */
	public function resolveTotalPage(): void
	{
		$totalPerPage = $this->parameter->getTotalListPerPage();
		$totalPerPage = $totalPerPage === 0 ? 1 : $totalPerPage;

		$total = (int) ceil($this->parameter->getTotalList()/$totalPerPage);
		
		$this->parameter->setTotalPage($total);
	}

	/**
	 * Handle not found page of the items list
	 *
	 * @param string $message
	 *
	 * @return void
	 */
	public function resolveNotFound($message = 'Page Not Found')
	{
		$total = $this->parameter->getTotalPage();
		$current = $this->parameter->getOffset();

		if ($current < PaginationParameter::INITIAL || $current > $total) {
			$this->parameter->setIsAvailable(false);
			$this->parameter->setPageNotFound($message);
		}
	}

	/**
	 * Handle pagination logic
	 * 
	 * @return void
	 */
	public function handle(): void
	{
		$this->handler->handle($this->parameter);
	}
}