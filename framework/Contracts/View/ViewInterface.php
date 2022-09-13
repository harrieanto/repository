<?php
namespace Repository\Component\Contracts\View;

/**
 * View Interface.
 * 
 * @package	 \Repository\Component\Contracts\View
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface ViewInterface
{
	/**
	 * Set template path
	 * 
	 * @param string|array $targets
	 * 
	 * @return \Repository\Component\View\View
	 */
	public function make(...$targets);

	/**
	 * Set view to the specific target without any render messages
	 * 
	 * @param string|array $targets
	 * 
	 * @return void
	 */
	public function to($targets);

	/**
	 * Set content variables to the specific view target
	 * 
	 * @param string|array $variables List of variable name
	 * @param mixed $contents List of variable value
	 * 
	 * @return void
	 */	
	public function with($variables, $contents = array());
}
