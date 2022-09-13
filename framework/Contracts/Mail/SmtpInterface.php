<?php
namespace Repository\Component\Contracts\Mail;

/**
 * Smtp Transport Interface.
 * 
 * @package	 \Repository\Component\Contracts\Mail
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface SmtpInterface
{
	public function send();
	
	public function isSent();
	
	public function getResponses();
	
	public function getResponse($key);
}