<?php
namespace Repository\Component\Contracts\Mail;

/**
 * Mail Connection Interface.
 * 
 * @package	 \Repository\Component\Contracts\Mail
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface IMailConnection
{
	public function connect(SmtpInterface $smtp);
}