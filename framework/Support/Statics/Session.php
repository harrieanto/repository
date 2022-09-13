<?php
namespace Repository\Component\Support\Statics;

/**
 * Session Static Invoker.
 * 
 * @package	  \Repository\Component\Support\Statics
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Session extends InvokeStatic
{
	/**
	 * @{inheritdoc}
	 * See \Repository\Component\Support\Statics\InvokeStatic::getStaticAccesor()
	 */
	public static function getStaticAccesor()
	{
		return 'session';
	}
}