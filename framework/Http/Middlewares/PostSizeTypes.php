<?php
namespace Repository\Component\Http\Middlewares;

/**
 * Post Size Types.
 * 
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class PostSizeTypes
{
	/** @var int The kilo bytes unit **/
	const KILO_BYTES = 1024;
	/** @var int The mega bytes unit **/
	const MEGA_BYTES = 1048576;
	/** @var int The giga bytes unit **/
	const GIGA_BYTES = 1073741824;
	/** @var string The kilo bytes type **/
	const KILO_BYTES_TYPE = 'K';
	/** @var string The mega bytes type **/
	const MEGA_BYTES_TYPE = 'M';
	/** @var string The giga bytes type **/
	const GIGA_BYTES_TYPE = 'G';
}