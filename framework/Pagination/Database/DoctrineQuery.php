<?php
namespace Repository\Component\Pagination\Database;

use Doctrine\ORM\Query as DoctrineQueryLanguage;

/**
 * Pagination Doctrine Query Language Transport
 * 
 * @package	  \Repository\Component\Pagination
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class DoctrineQuery extends Query
{
	/**
	 * Set DQL instance
	 * @param \Doctrine\ORM\Query $query
	 */
	public function __construct(DoctrineQueryLanguage $query)
	{
		$this->setDefaultQuery($query);
	}

	/**
	 * Get DQL instance
	 * @param \Doctrine\ORM\Query $query
	 */
	public function getQuery()
	{
		return $this->getDefaultQuery();
	}
}