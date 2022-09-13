<?php
namespace Repository\Component\Mail;

use RuntimeException;
use Psr\Http\Message\UriInterface;
use Repository\Component\Mail\Sender;
use Repository\Component\Contracts\Mail\SmtpInterface;
use Repository\Component\Contracts\Mail\IMailConnection;

/**
 * Smtp Transport.
 *
 * @package	  \Repository\Component\Mail
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Smtp implements SmtpInterface
{
	/**
	 * The smtp conection
	 * @var \Repository\Component\Contracts\Mail\IMailConnection $connection
	 */
	protected $connection;
	
	/**
	 * Uri instance
	 * @var \Psr\Http\Message\UriInterface $uri
	 */
	protected $uri;

	/**
	 * The protocol conection type
	 * @var string $connectionType
	 */
	protected $connectionType = 'tls';

	/**
	 * The sender host
	 * @var string $senderHost
	 */
	protected $senderHost = 'localhost';

	/**
	 * The smtp host target
	 * @var string $target
	 */
	protected $target;

	/**
	 * The smtp username
	 * @var string $username
	 */
	protected $username;

	/**
	 * The smtp password
	 * @var string $password
	 */
	protected $password;

	/**
	 * The smtp host port
	 * @var string $connectionType
	 */
	protected $port= 25;

	/**
	 * The connection timeout
	 * @var string $connectionType
	 */
	protected $timeout = 45;

	/**
	 * @param \Psr\Http\Message\UriInterface $uri
	 * @param \Repository\Component\Contracts\Mail\IMailConnection $connection
	 */
	public function __construct(UriInterface $uri, IMailConnection $connection)
	{
		if (empty($connection->getMailerInstance()->Recipient()))
			throw new \RuntimeException(
				"Can't send email. Recipient not set."
			);

		$this->connection = $connection;
		$this->uri = $uri;
	}
	
	public function connect()
	{
		$connection = $this->connection->connect($this);
		
		return $connection;
	}

	/**
	 * Send email
	 * 
	 * @return \Repository\Component\Mail\Smtp
	 */	
	public function send()
	{
		$this->connect();
		$this->connection->send();
		
		return $this;
	}
	/**
	 * Determine if the email has been sent
	 * 
	 * @return bool
	 */	
	public function isSent()
	{
		$sent = $this->connection->isSent();
		
		return $sent;
	}

	/**
	 * Get connection responses
	 * 
	 * @return array
	 */		
	public function getResponses()
	{
		$responses = $this->connection->getResponses();
		
		return $responses;
	}

	/**
	 * Get connection response by key
	 * 
	 * @param string $key
	 * 
	 * @return string Response message
	 */	
	public function getResponse($key)
	{
		$response = $this->connection->getResponse($key);
		
		return $response;
	}

	/**
	 * Set sender host
	 * 
	 * @return \Repository\Component\Mail\Smtp
	 */		
	public function setSenderHost($host)
	{
		$this->senderHost = $host;
	}

	/**
	 * Get email sender host
	 * 
	 * @return string
	 */		
	public function getSenderHost()
	{
		return $this->senderHost;
	}

	/**
	 * Set smtp username
	 * 
	 * @return \Repository\Component\Mail\Smtp
	 */			
	public function setUsername($username)
	{
		$this->username = $username;
		
		return $this;
	}

	/**
	 * Get smtp username
	 * 
	 * @return string
	 */		
	public function getUsername()
	{
		return (string) $this->username;
	}

	/**
	 * Set smtp password
	 * 
	 * @return \Repository\Component\Mail\Smtp
	 */		
	public function setPassword($password)
	{
		$this->passsword = $password;
		
		return $this;
	}

	/**
	 * Get smtp password
	 * 
	 * @return string
	 */			
	public function getPassword()
	{
		return (string) $this->password;
	}

	/**
	 * Set smtp host port
	 * 
	 * @return \Repository\Component\Mail\Smtp
	 */		
	public function setPort($port)
	{
		$this->port = $port;
	}

	/**
	 * Get smtp host port
	 * 
	 * @return int
	 */		
	public function getPort()
	{
		return (int) $this->port;
	}

	/**
	 * Set connection timeout
	 * 
	 * @return \Repository\Component\Mail\Smtp
	 */		
	public function setTimeout($timeout)
	{
		$this->timeout = $timeout;
	}

	/**
	 * Get connection timeout
	 * 
	 * @return string
	 */	
	public function getTimeout()
	{
		return (int) $this->timeout;
	}

	/**
	 * Set smtp target host
	 * 
	 * @return \Repository\Component\Mail\Smtp
	 */		
	public function setTarget($target)
	{
		$this->target = $this->resolveTarget($this->uri, $target);
		
		return $this;
	}

	/**
	 * Get smtp target host
	 * 
	 * @return string
	 */	
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * Resolve target host by protocol connection type
	 * 
	 * @return void
	 */	
	public function resolveTarget(UriInterface $uri, $target)
	{
		$scheme = $this->getConnectionType();
		$uri->withScheme($scheme);
		$uri->withHost($target);
		
		$target = $uri->getUri();
		
		return $target;
	}

	/**
	 * Get protocol connection type
	 * 
	 * @return string
	 */		
	public function getConnectionType()
	{
		return $this->connectionType;
	}

	/**
	 * Set protocol connection type
	 * 
	 * @return \Repository\Component\Mail\Smtp
	 */		
	public function setConnectionType($type = 'tls')
	{
		$this->connectionType = $type;
		
		return $this;
	}
}