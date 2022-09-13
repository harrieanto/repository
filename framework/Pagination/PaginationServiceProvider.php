<?php
namespace Repository\Component\Pagination;

use Repository\Component\Support\ServiceProvider;
use Repository\Component\Pagination\Handlers\Doctrine;
use Repository\Component\Pagination\Handlers\DoctrineSql;
use Repository\Component\Pagination\Handlers\ArrayCollection;

/**
 * Pagination Service Provider
 * 
 * @package	  \Repository\Component\Pagination
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class PaginationServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerPaginationParameter();
		$this->registerArrayCollectionHandler();
		$this->registerDoctrineHandler();
		$this->registerDoctrineSqlHandler();
		$this->registerPageMarker();
		$this->registerPageFinder();

		$this->app->singleton('pagination', function($app) {
			return new Manager($app);
		});

		$this->app->singleton('pagination.doctrine', function($app) {
			$pagination = new Manager($app);
			return $pagination->createDoctrineDriver();
		});

		$this->app->singleton('pagination.doctrine.sql', function($app) {
			$pagination = new Manager($app);
			return $pagination->createDoctrineSqlDriver();
		});
	}
	
	/**
	 * Register doctrine pagination handler
	 *
	 * @return void
	 */
	public function registerArrayCollectionHandler()
	{
		$this->app->singleton('pagination.handler.collection', function($app) {
			return new ArrayCollection;
		});
	}

	/**
	 * Register doctrine pagination handler
	 *
	 * @return void
	 */
	public function registerDoctrineHandler()
	{
		$this->app->singleton('pagination.handler.doctrine', function($app) {
			return new Doctrine;
		});
	}

	/**
	 * Register doctrine pagination handler
	 *
	 * @return void
	 */
	public function registerDoctrineSqlHandler()
	{
		$this->app->singleton('pagination.handler.doctrine.sql', function($app) {
			return new DoctrineSql;
		});
	}

	/**
	 * Register Paginate service
	 *
	 * @return void
	 */
	public function registerPaginationParameter()
	{
		$this->app->singleton('pagination.parameter', function($app) {
			return new PaginationParameter();
		});
	}

	/**
	 * Register Marker service
	 *
	 * @return void
	 */
	public function registerPageMarker()
	{
		$this->app->singleton('pagination.marker', function($app) {
			return new Marker($app['pagination.parameter']);
		});
	}

	/**
	 * Register Finder service
	 *
	 * @return void
	 */
	public function registerPageFinder()
	{
		$this->app->bind('pagination.finder', function($app) {
			return new Finder($app['pagination.parameter']);
		});
	}
}