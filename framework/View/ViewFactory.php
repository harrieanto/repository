<?php
namespace Repository\Component\View;

use Repository\Component\Filesystem\Filesystem;
use Repository\Component\View\Compiler\CompilerFactory;

/**
 * View Factory.
 * 
 * @package	  \Repository\Component\View
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ViewFactory
{
	public static function create()
	{
		$fs = new Filesystem;
		$view = new View($fs, new CompilerFactory($fs));
		
		return $view;
	}
}