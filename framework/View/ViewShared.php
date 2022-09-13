<?php
namespace Repository\Component\View;

use Repository\Component\Contracts\Container\ContainerInterface;
use Repository\Component\Contracts\View\ViewInterface;

/**
 * View Shared Handler.
 * 
 * @package	  \Repository\Component\View
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
abstract class ViewShared
{
	/**
	 * @var \Repository\Component\View\View $view
	 */
	protected $view;

	/**
	 * @var \Repository\Component\Contracts\Container\ContainerInterface $app
	 */
	protected $app;

	/**
	 * @param \Repository\Component\View\View $view
	 */
	public function __construct(ViewInterface $view)
	{
		$this->view = $view;
	}

	/**
	 * @param \Repository\Component\Contracts\Comtainer\ContainerInterface $app
	 */	
	public function registerApp(ContainerInterface $app)
	{
		$this->app = $app;
	}

	/**
	 * Register shared variables to the view handler
	 * 
	 * @return void
	 */	
	abstract function registerSharedVariable();

	/**
	 * Get defined shared variable
	 * 
	 * @return null|\Repository\Component\Collectio\Collection
	 */	
	public function getSharedVariable()
	{
		return $this->view->getSharedVariable();
	}
}