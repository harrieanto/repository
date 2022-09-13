<?php
namespace Repository\Component\Pagination;

/**
 * Pagination Marker.
 * 
 * @package	  \Repository\Component\Pagination
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Marker
{
	/** @var int The digger number subtractor **/
	const DIG_SUBTRACTOR = 5;
	
	/** @var int The digger number accumulator **/
	const DIG_ACCUMULATOR = 6;

	/** @var string The digger string separator **/	
	const DIG_STRING_SEPARATOR = '...';
	
	/**
	 * Setup
	 * @param \Repository\Component\Pagination\parameter $parameter
	 */
	public function __construct(PaginationParameter $parameter)
	{
		$this->parameter = $parameter;
	}

	/**
	 * Resolve the next and previous pointer position
	 * 
	 * @return \Repository\Component\Pagination\PageCollection
	 */	
	public function resolvePointerPosition()
	{
		//Get the current accessed page number as pointer position
		$position = $this->parameter->getOffset();
		$totalPage = $this->parameter->getTotalPage();
		
		//If the position less than the number of initial page
		//We'll setup pointer position as 1
		//So whatever page url inputed by user it won't change
		//our pagination behaviour 
		if ($position <= PaginationParameter::INITIAL && $this
			->parameter
			->getTotalListPerPage() < $this
			->parameter
			->getOffset()) {
			
			$position = PaginationParameter::INITIAL;
		}
		
		//If the items still available to paginating
		//We'll just passing the pointer position
		//to the `parameter` value object
		if ($this->parameter->isAvailable()) {
			$this->parameter->setNextPosition($position+PaginationParameter::INITIAL);
			$this->parameter->setPreviousPosition($position-PaginationParameter::INITIAL);
			
			//If the current position is equal with total page have parameterd
			//We would set the next position poiter to null
			//So we could control our presentation logic without knowing how to handle in their own
			//The rules above used when the current position is equal with initial page
			if ($position === $totalPage) {
				$this->parameter->setNextPosition(null);
			}
			
			//Here we h'll handle when the current position match with initial page
			if ($position == PaginationParameter::INITIAL) {
				$this->parameter->setPreviousPosition(null);
			}
		}
		
		return $this;
	}

	/**
	 * Generate the number list of total page
	 * 
	 * @return \Repository\Component\Pagination\PageCollection
	 */	
	public function resolveNumberList()
	{
		$numberLists = array();
		$total = $this->parameter->getTotalPage();
		$current = $this->parameter->getOffset();
		
		$initialRange = $current - self::DIG_SUBTRACTOR;
		$initialRange = max(2, $initialRange);
		$middleRange = $current + self::DIG_ACCUMULATOR;
		$middleRange = min($middleRange, $total);
		
		$numberLists[] = 1;
		
		if ($initialRange > 2) {
			$numberLists[] = self::DIG_STRING_SEPARATOR;
		}
		
		for ($i = $initialRange; $i <= $middleRange; $i++) {
			$numberLists[] = $i;
		}

		if ($middleRange !== $total) {
			$numberLists[] = self::DIG_STRING_SEPARATOR;
			$numberLists[] = (int) $total;
		}
		
		if ($this->parameter->isAvailable()) {
			$this->parameter->setNumberListMarker($numberLists);
		}

		return $this;
	}

	/**
	 * Generate previous page marker
	 * 
	 * @return \Repository\Component\Pagination\PageCollection
	 */	
	public function resolvePreviousMarker()
	{
		$total = $this->parameter->getTotalPage();
		$current = $this->parameter->getOffset();
		
		if ($current > PaginationParameter::INITIAL && $current <= $total) {

			$this->parameter->setPreviousMarker($this
				->parameter
				->getPreviousMarker()
			);

		} else {
			$this->parameter->setPreviousMarker(null);
		}
		
		return $this;
	}

	/**
	 * Generate next page marker
	 * 
	 * @return \Repository\Component\Pagination\PageCollection
	 */	
	public function resolveNextMarker()
	{
		$total = $this->parameter->getTotalPage();
		$current = $this->parameter->getOffset();
		
		if ($current >= PaginationParameter::INITIAL && $current < $total) {

			$this->parameter->setNextMarker($this
				->parameter
				->getNextMarker()
			);
		} else {
			$this->parameter->setNextMarker(null);
		}
		
		return $this;
	}
}