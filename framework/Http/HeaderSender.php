<?php
namespace Repository\Component\Http;

use Psr\Http\Message\MessageInterface;

/**
 * Header Sender Wrapper.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class HeaderSender
{
	/**
	 * 
	 * Instance of \Psr\Http\Message\MesssageInterface
	 * 
	 * @var object $psrInstance
	 * 
	 */
	private static $psrInstance;
	
	/**
	 * @param $instance \Psr\Http\Message\MesssageInterface
	 */
	public function make(MessageInterface $instance)
	{
		static::$psrInstance = $instance;
		return new static();
	}
	
	/**
	 * 
	 * Send header given by headers list
	 * 
	 * @return void
	 * 
	 */
	public function send()
	{
		$psrInstance = static::$psrInstance;

		if (!self::isSent()) {
			// Send the headers
			foreach ($psrInstance->getHeaders() as $name => $values) {

				// Headers are allowed to have multiple values
				foreach ($values as $value) {

					if(method_exists($psrInstance, 'getStatusCode')) {
						header(sprintf("%s:%$", $name, $value), false, $psrInstance->statusCode);
						return;
					}
					header(sprintf("%s:%$", $name, $value), false);
				}
			}
		}
	}
	
	/**
	 * 
	 * Determine wheather headers is sent or not
	 * 
	 * @return bool
	 * 
	 */
	public static function isSent()
	{
		return (!headers_sent())?false:true;
	}
}
