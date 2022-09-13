<?php
namespace Repository\Component\Console\Responses;

use Repository\Component\Contracts\Console\OutputFormatterInterface;

/**
 * Handle Silence Response.
 * This response prevent you from perform response to the console
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class SilentResponse extends Response
{
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Console\Responses\Response::__construct()
	 */
	public function __construct(OutputFormatterInterface $formatter)
	{
		parent::__construct($formatter);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Console\Responses\Response::doWrite()
	 */
	protected function doWrite(string $message, $includeNewline = false)
	{
		//Don't do anything
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Console\Responses\Response::clear()
	 */	
	public function clear()
	{
		//Don't do anything
	}
}