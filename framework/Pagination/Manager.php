<?php
namespace Repository\Component\Pagination;

use Repository\Component\Support\Manager as PaginationManager;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * Pagination Manager
 *
 * @package	  \Repository\Component\Pagination
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Manager extends PaginationManager
{
	/**
	 * @inheritdoc
	 * See \Repository\Component\Support\Manager
	 */	
	public function __construct(ContainerInterface $app)
	{
		$this->app = $app;
	}
	
	/**
	 * @inheritdoc
	 * See \Repository\Component\Support\Manager::getDefaultDriver
	 */	
	public function getDefaultDriver()
	{
		$driver = $this->getPaginationParameter('default');
		
		return $driver;
	}
	
	/**
	 * Get pagination parameter by the given key
	 * 
	 * @param string $key
	 * 
	 * @return string
	 */	
	private function getPaginationParameter($key)
	{
		$param = $this->app['config']['pagination'][$key];
		
		return $param;
	}

	/**
	 * Create pagination item collection based
	 * 
	 * @return \Repository\Component\Pagination\Pagination
	 */
	public function createItemDriver()
	{
		$app = $this->app;

		$pagination = new Pagination(
			$app['pagination.parameter'], 
			$app['pagination.handler.collection'], 
			$app['request']
		);

		$factory = new PaginationFactory(
			$app['pagination.parameter'], 
			$pagination, 
			$app['pagination.marker']
		);
		
		$factory->withFinder($app['pagination.finder']);
			
		return $factory;
	}

	/**
	 * Create pagination database based
	 * 
	 * @return \Repository\Component\Pagination\Pagination
	 */
	public function createDoctrineDriver()
	{
		$app = $this->app;

		$pagination = new Pagination(
			$app['pagination.parameter'], 
			$app['pagination.handler.doctrine'], 
			$app['request']
		);

		$factory = new PaginationFactory(
			$app['pagination.parameter'], 
			$pagination, 
			$app['pagination.marker']
		);
			
		return $factory;
	}

	/**
	 * Create pagination database based
	 * 
	 * @return \Repository\Component\Pagination\Pagination
	 */
	public function createDoctrineSqlDriver()
	{
		$app = $this->app;

		$pagination = new Pagination(
			$app['pagination.parameter'], 
			$app['pagination.handler.doctrine.sql'], 
			$app['request']
		);

		$factory = new PaginationFactory(
			$app['pagination.parameter'], 
			$pagination, 
			$app['pagination.marker']
		);
			
		return $factory;
	}
}