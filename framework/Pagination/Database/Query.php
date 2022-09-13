<?php
namespace Repository\Component\Pagination\Database;

/**
 * Pagination Query Transport
 * 
 * @package	  \Repository\Component\Pagination
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
abstract class Query
{
	/**
	 * The default query used for pagination
	 * @var mixed $defaultQuery
	 */
	protected $defaultQuery;

	/**
	 * Set defualt query
	 * 
	 * @param mixed $query
	 * 
	 * @return void
	 */
	public function setDefaultQuery($query): void
	{
		$this->defaultQuery = $query;
	}

	/**
	 * Get defualt query
	 * 
	 * @return mixed
	 */	
	public function getDefaultQuery()
	{
		return $this->defaultQuery;
	}
}