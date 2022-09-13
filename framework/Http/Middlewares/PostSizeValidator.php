<?php
namespace Repository\Component\Http\Middlewares;

use Psr\Http\Message\RequestInterface;

/**
 * Post Size Validator.
 * 
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class PostSizeValidator
{
	/**
	 * The request instance
	 * @var \Psr\Http\Message\ServerRequest $request
	 */
	private $request;

	/**
	 * @param \Psr\Http\Message\ServerRequest $request
	 */
	public function __construct(RequestInterface $request)
	{
		$this->request = $request;
	}

	/**
	 * Validate requested post size
	 * 
	 * @throw \Repository\Component\Http\Middlewares\Exception\PostTooLargeException
	 *  
	 * @return bool
	 */	
	public function validate()
	{
		$maximal = $this->getMaximalSize();
		
		$postSize = (int) $this->request->getHeaderLine('Content-Length');
		
		if ($maximal > 0 && $postSize > $maximal) {
			return false;
		}
		
		return true;
	}

	/**
	 * Get allowed maximal post size
	 * 
	 * @return int
	 */	
	public function getMaximalSize()
	{
		$size = ini_get('post_max_size');
		
		$type = mb_strtoupper(mb_substr($size, -1));
		
		$size = (int) $size;

		switch ($type) {
			case PostSizeTypes::KILO_BYTES_TYPE:
				$size = $size * PostSizeTypes::KILO_BYTES; break;
			case PostSizeTypes::MEGA_BYTES_TYPE:
				$size = $size * PostSizeTypes::MEGA_BYTES; break;
			case PostSizeTypes::GIGA_BYTES_TYPE:
				$size = $size * PostSizeTypes::GIGA_BYTES; break;
		}
		
		return (int) $size;
	}
}