<?php
namespace Repository\Component\Log;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Logger Factory
 *
 * @package	  \Repository\Component\Log
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Logger implements LoggerAwareInterface
{
	/**
	 * See \Psr\Log\LoggerAwareTrait
	 */
	use LoggerAwareTrait;

	/**
	 * Get concrete logger instance
	 * 
	 * @return \Psr\Log\LoggerInterface
	 */
	public function getInstance()
	{
		return $this->logger;
	}
}