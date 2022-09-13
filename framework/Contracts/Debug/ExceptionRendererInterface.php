<?php
namespace Repository\Component\Contracts\Debug;

/**
 * Debug Exception Renderer Interface.
 * 
 * @package	 \Repository\Component\Contracts\Debug
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface ExceptionRendererInterface
{
	public function handle(\Throwable $exception);
}