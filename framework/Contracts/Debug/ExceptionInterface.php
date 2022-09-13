<?php
namespace Repository\Component\Contracts\Debug;

/**
 * Debug Exception Interface.
 * 
 * @package	 \Repository\Component\Contracts\Debug
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface ExceptionInterface
{
	public function handle($exception);
	
	public function register();
}