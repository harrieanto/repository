<?php
namespace Repository\Component\Pagination;

use Traversable;
use Psr\Http\Message\UriInterface;
use Repository\Component\Pagination\Finder;
use Repository\Component\Pagination\Database\Query;

/**
 * Pagination Value Object
 * 
 * @package	  \Repository\Component\Pagination
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class PaginationParameter
{
	/** The initial page number */
	const INITIAL = 1;
	
	/**
	 * List of item in current page request
	 * @var \Traversable $item
	 */
	protected $item;

	/**
	 * The collection data to be paginate
	 * @var \Traversable $collection
	 */
	protected $collection;

	/**
	 * Total items
	 * @var int $total
	 */
	protected $totalList = 0;

	/**
	 * Total page of the given items
	 * @var int $total
	 */
	protected $totalListPerPage = 10;

	protected $offsetListPerPage = 0;

	/**
	 * Uri instance
	 * @var \Psr\Http\Message\UriInterface $uri
	 */	
	protected $uri;

	/**
	 * The previous page marker
	 * @var string $previousMarker
	 */
	protected $previousMarker = 'Previous';

	/**
	 * The next page marker
	 * @var string $previousMarker
	 */
	protected $nextMarker = 'Next';

	/**
	 * The decremented previous pointer position
	 * @var int $prevPosition
	 */	
	protected $prevPosition;

	/**
	 * The accumulated previous pointer position
	 * @var int $nextPosition
	 */	
	protected $nextPosition;

	/**
	 * Page numbers list
	 * @var array $previousMarker
	 */
	protected $numberLists = array();

	/**
	 * Item available
	 * @var bool $available
	 */
	protected $available = true;

	/**
	 * Not found message
	 * @var string $notFound
	 */
	protected $notFound;

	/**
	 * The item limit per page
	 * @var int $previousMarker
	 */
	protected $offset = 10;

	/**
	 * Finder instance
	 * 
	 * @var \Repository\Component\Pagination\Finder $finder
	 */
	 protected $finder;

	/**
	 * Query instance
	 * 
	 * @var \Repository\Component\Pagination\Query $query
	 */
	 protected $query;

	 protected $dbInstance;

	 public function setDbInstance($dbInstance)
	 {
		if (!is_object($dbInstance)) {
			throw new \InvalidArgummentException('Database instance must be object');
		}

		$this->dbInstance = $dbInstance;
	 }
	 
	 public function getDbInstance()
	 {
		return $this->dbInstance;
	 }

	 /**
	 * Set the number of items limitation per page
	 * 
	 * @param int $limit
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */
	public function setOffset(int $offset = 50)
	{
		if ($offset < 1) {
			$offset = 1;
		}

		$this->offset = $offset;

		return $this;
	}

	/**
	 * Get the number of items limitation per page
	 * 
	 * @return int
	 */
	public function getOffset()
	{
		return $this->offset;
	}

	/**
	 * Set the number of total items
	 * 
	 * @param int $total
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */
	public function setTotalListPerPage(int $total)
	{
		$this->totalListPerPage = $total;
		return $this;
	}

	/**
	 * Get the number of total items
	 * 
	 * @return int
	 */
	public function getTotalListPerPage()
	{
		return $this->totalListPerPage;
	}

	/**
	 * Set the number of total items
	 * 
	 * @param int $total
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */
	public function setOffsetListPerPage(int $total)
	{
		$this->offsetListPerPage = $total;
		return $this;
	}

	/**
	 * Get the number of total items
	 * 
	 * @return int
	 */
	public function getOffsetListPerPage()
	{
		return $this->offsetListPerPage;
	}

	/**
	 * Set request uri
	 * 
	 * @param \Psr\Http\Message\UriInterface $uriRequest
	 * 
	 * @return void
	 */
	public function setUri(UriInterface $uriRequest)
	{
		$this->uri = $uriRequest;
	}

	/**
	 * Get request uri insttance
	 * 
	 * @return \Psr\Http\Message\UriInterface
	 */
	public function getUriInstance()
	{
		return $this->uri;
	}

	/**
	 * Set array collection by the given collection
	 * 
	 * @param \Trraversable $collection
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */
	public function setArrayCollection(Traversable $collection)
	{
		$this->collection = $collection;
		
		return $this;
	}
	/**
	 * Get array collection
	 * 
	 * @return \Traversable
	 */
	public function getArrayCollection()
	{
		return $this->collection;
	}

	/**
	 * Set divided items to the current page number
	 * 
	 * @param \Trraversable $item
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */
	public function setPageItems(Traversable $item)
	{
		$this->item = $item;
		return $this;
	}

	/**
	 * Get divided items for the current page number
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */
	public function getPageItems()
	{
		return $this->item;
	}

	/**
	 * Set current requested page number
	 * 
	 * @param int $number
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */
	public function setCurrentPageNumber($number)
	{
		$this->currentPage = $number;
		return $this;
	}

	/**
	 * Get current requested page number
	 * 
	 * @return int
	 */
	public function getCurrentPageNumber()
	{
		return $this->currentPage;
	}

	/**
	 * Set the number of total page of the items
	 * 
	 * @param int $total
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */	
	public function setTotalPage($total)
	{
		$this->totalPage = $total;
		return $this;
	}

	/**
	 * Get the number of total page of the items
	 * 
	 * @return int
	 */	
	public function getTotalPage()
	{
		return $this->totalPage;
	}

	/**
	 * Set the number of total items row per page
	 * 
	 * @param int $total
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */	
	public function setTotalList(int $total)
	{
		$this->totalList = $total;
		return $this;
	}

	/**
	 * Get the number of total items row per page
	 * 
	 * @return int
	 */	
	public function getTotalList()
	{
		return $this->totalList;
	}

	/**
	 * Set page availaibility
	 * 
	 * @param bool $available
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */	
	public function setIsAvailable(bool $available = true)
	{
		$this->available = $available;
		return $this;
	}

	/**
	 * Determine if the requested page number available
	 * 
	 * @return bool
	 */	
	public function isAvailable()
	{
		return $this->available;
	}

	/**
	 * Set page availaibility
	 * 
	 * @param bool $available
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */	
	public function setPageNotFound($message)
	{
		$this->notFound = $message;
		return $this;
	}

	/**
	 * Get not found message
	 * 
	 * @return string
	 */	
	public function getPageNotFound()
	{
		return $this->notFound;
	}

	/**
	 * Set previous page marker
	 * 
	 * @param string $marker
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */	
	public function setPreviousMarker($marker)
	{
		$this->previousMarker = $marker;
		return $this;
	}

	/**
	 * Get previous page marker
	 * 
	 * @return string
	 */	
	public function getPreviousMarker()
	{
		return $this->previousMarker;
	}

	/**
	 * Set next page marker
	 * 
	 * @param string $marker
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */	
	public function setNextMarker($marker)
	{
		$this->nextMarker = $marker;
		return $this;
	}

	/**
	 * Get next page marker
	 * 
	 * @return string
	 */	
	public function getNextMarker()
	{
		return $this->nextMarker;
	}

	/**
	 * Set previous page marker
	 * 
	 * @param string $marker
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */	
	public function setPreviousPosition($position)
	{
		$this->prevPosition = $position;
		return $this;
	}

	/**
	 * Get previous page marker
	 * 
	 * @return string
	 */	
	public function getPreviousPosition()
	{
		return $this->prevPosition;
	}

	/**
	 * Set next page marker
	 * 
	 * @param string $marker
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */	
	public function setNextPosition($position)
	{
		$this->nextPosition = $position;
		return $this;
	}

	/**
	 * Get next page marker
	 * 
	 * @return string
	 */	
	public function getNextPosition()
	{
		return $this->nextPosition;
	}

	/**
	 * Set the number list of total page
	 * 
	 * @param array $numberLists
	 * 
	 * @return \Repository\Component\Pagination\Paginate
	 */	
	public function setNumberListMarker(array $numberLists)
	{
		$this->numberLists = $numberLists;
		return $this;
	}

	/**
	 * Get the number list of total page
	 * 
	 * @return array
	 */	
	public function getNumberListMarker()
	{
		return $this->numberLists;
	}

	/**
	 * Set query instance
	 * 
	 * @param \Repository\Component\Pagination\Database\Query $query
	 * 
	 * @return void
	 */
	public function withQuery(Query $query)
	{
		$this->query = $query;
	}

	/**
	 * Get query instance
	 * 
	 * @return \Repository\Component\Pagination\Database\Query
	 */	
	public function getQueryInstance()
	{
		return $this->query ?? new Query;
	}

	/**
	 * Set finder instance
	 * 
	 * @param \Repository\Component\Pagination\Finder $finder
	 * 
	 * @return void
	 */
	public function withFinder(Finder $finder)
	{
		$this->finder = $finder;
	}
	
	/**
	 * Get finder instance
	 * 
	 * @throw \Repository\Component\Pagination\Exception\PageException
	 * 
	 * @return \Repository\Component\Pagination\Finder
	 */
	public function getFinder()
	{
		if($this->finder === null ) {
			throw new PaginationException(Finder::class. " not set");
		}
		
		return $this->finder;
	}
}