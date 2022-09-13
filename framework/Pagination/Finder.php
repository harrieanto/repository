<?php
namespace Repository\Component\Pagination;

use Closure;
use Repository\Component\Support\Str;
use Repository\Component\Collection\Collection;

/**
 * Find the number of item in the page collection.
 *
 * @package	  \Repository\Component\Pagination
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Finder
{
	/**
	 * @var \Repository\Component\Pagination\PaginationParameter $parameter
	 */
	private $parameter;
	
	/**
	 * @param \Repository\Component\Pagination\PaginationParameter $parameter
	 */
	public function __construct(PaginationParameter $parameter)
	{
		$this->parameter = $parameter;
	}

	/**
	 * Find the given item in the collections
	 * 
	 * @param \Repository\Component\Collection\Collection $collection 
	 * @param string $item the item to find
	 * @param bool $sensitive Determine whether finding item process
	 * find by case-sensitive or not
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */	
	private function find(Collection $collection, $item, $sensitive = true)
	{
		//Make sure that item still available to paginating
		//So that we only do the finding process
		//when item is available
		if ($this->parameter->isAvailable()) {
			$collection = $collection->filter(function($collection) use ($item, $sensitive) {
				if ($this->hasFinderContext()) {
					$context = $this->context;
					
					if (isset($collection[$context])) {
						$collection = $collection[$context];
					} else {
						throw new \Exception("The given context [$context] is not found.");
					}
				}
				
				//Boolean `true` indicating that we'll find the item
				//in the collections as case-sensitive
				//`false` otherwise
				if ($sensitive === false) {
					$item = Str::lower($item);
					$collection = Str::lower($collection);
				}
				
				if (Str::contains($collection, $item)) {
					return $collection;
				}
			});
			
			return $collection;
		}
	}

	/**
	 * 
	 * Find the given item in the current page where the collections have limited
	 *  
	 * @param string $item the item to find
	 * @param bool $sensitive Determine whether finding item process
	 * find by case-sensitive or not
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */	
	public function findInCurrentPageBy($item, Closure $callback, $sensitive = true)
	{
		$collection = $this->parameter->getPageItems();
		$collection = $callback($collection);
		
		return $this->find($collection, $item, $sensitive);
	}
	
	/**
	 * 
	 * Find the given item in all page where the collections exists
	 *  
	 * @param string $item the item to find
	 * @param bool $sensitive Determine whether finding item process
	 * find by case-sensitive or not
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */	
	public function findInAllPageBy($item, $context = false, $sensitive = true)
	{
		$this->setFinderContext($context);
		$collection = $this->parameter->getArrayCollection();
		
		return $this->find($collection, $item, $sensitive);
	}
	
	public function setFinderContext($context = false)
	{
		if ($context !== false) {
			$this->context = $context;
		}
	}
	
	private function hasFinderContext()
	{
		if ($this->context !== null || $this->context !== false) {
			if (is_string($this->context)) return true;
			return false;
		}
		
		return false;
	}
}