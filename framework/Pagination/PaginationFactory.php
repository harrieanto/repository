<?php
namespace Repository\Component\Pagination;

use Repository\Component\Pagination\Exception\PaginationException;
use Repository\Component\Contracts\Pagination\PaginationInterface;

/**
 * Pagination Factory
 * 
 * @package	  \Repository\Component\Pagination
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class PaginationFactory
{
	/**
	 * The pagination instance
	 * @var \Repository\Component\Contracts\Pagination\PaginationInterface $pagination
	 */
	protected $pagination;

	/**
	 * The pagination parameter
	 * @var \Repository\Component\Pagination\PaginationParameter $parameter
	 */
	protected $parameter;

	/**
	 * @param \Repository\Component\Pagination\PaginationParamater $parameter
	 * @param \Repository\Component\Contracts\Pagination\PaginationInterface $pagination
	 * @param \Repository\Component\Pagination\Marker $marker
	 */
	public function __construct(
		PaginationParameter $parameter, 
		PaginationInterface $pagination, 
		Marker $marker)
	{
		$this->parameter = $parameter;
		$this->pagination = $pagination;
		$this->marker = $marker;
	}

	/**
	 * Resolve pagination
	 * 
	 * @return void
	 */
	public function paginate()
	{
		$this->pagination->resolveOffsetListPerPage();
		//Initialise pagination
		$this->pagination->handle();
		//Resolve total page of the total items
		$this->pagination->resolveTotalPage();
		//Handle not found message
		//when item in the accessed page not available
		$this->pagination->resolveNotFound();
		//Handle next and previous pointer position
		$this->marker->resolvePointerPosition();
		//Resolve ordered number of the total page
		$this->marker->resolveNumberList();
		//Handle previous marker
		$this->marker->resolvePreviousMarker();
		//Handle next marker	
		$this->marker->resolveNextMarker();
	}
	
	/**
	 * Dynamically handle pagination builder from this class
	 * 
	 * @param string $methodName
	 * @param array $params
	 *  
	 * @throw \Repository\Component\Pagination\Exception\PaginationException
	 *  
	 * @return mixed
	 */
	public function __call($methodName, $params)
	{
		$methodAvailable = method_exists($this->parameter, $methodName);
		
		if(!$methodAvailable) {
			$this->pagination->{$methodName}(...$params);
		} else if ($methodAvailable) {
			return $this->parameter->{$methodName}(...$params);
		} else {
			throw new PaginationException("Method {$methodName} not exists");
		}
	}
}