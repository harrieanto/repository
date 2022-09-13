<?php
namespace Repository\Component\Mail\Sender;

use RuntimeException;
use Repository\Component\Mail\Smtp;
use Repository\Component\Mail\Mailer;
use Repository\Component\Contracts\Mail\SmtpInterface;
use Repository\Component\Contracts\Mail\IMailConnection;

/**
 * Send Email Through Socket Connection.
 *
 * @package	  \Repository\Component\Mail
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Socket implements SmtpInterface, IMailConnection
{
	/**
	 * The smtp socket conection
	 * @var \Repository\Component\Contrracts\Mail\IMailConnection $connection
	 */
	protected $connection;

	/**
	 * The socket responses
	 * @var array $responses
	 */
	protected $responses = array();

	/**
	 * The mailer instance
	 * @var \Repository\Component\Mail\Mailer $mailer
	 */
	protected $mailer;

	/**
	 * @param \Repository\Component\Mail\Mailer $mailer
	 */
	public function __construct(Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	/**
	 * Get mailer instance
	 * @return \Repository\Component\Mail\Mailer
	 */
	public function getMailerInstance()
	{
		return $this->mailer;
	}

	/**
	 * Set request to the socket connection
	 * 
	 * @param string $arguments
	 * 
	 * @return \Repository\Component\Contracts\Mail\IMailConnection
	 */		
	public function sendRequest($arguments)
	{
		fputs($this->connection, arguments);
		
		return $this;
	}

	/**
	 * Open mail host connection
	 * 
	 * @param \Repository\Component\Contracts\Mail\SmtpInterface
	 * 
	 * @return \Repository\Component\Contracts\Mail\IMailConnection
	 */
	public function connect(SmtpInterface $smtp)
	{
		$target = $smtp->getTarget();

		//open socket connection
		$connection = fsockopen( 
			$target, 
			$smtp->getPort(), 
			$errno, 
			$errstr, 
			$smtp->getTimeout()
		);

		//check connection fail or not
		$failed = "Unable to connect over {$target} : ".socket_strerror(socket_last_error());
		
		if (!$connection) throw new RuntimeException($failed); 

		$this->connection = $connection;
		
		return $this;
	}

	/**
	 * @inheritdoc
	 * See \Repository\Component\Contrracts\Mail\SmtpInterface
	 */	
	public function send()
	{
		//have to say EHLO as initial command when requesting authentication
		$host = $this->mailer->getSenderHost();
		$this->sendRequest("EHLO $host ". Mailer::RN);
		$this->setResponse('first_ehlo_response' );

		//We should say EHLO as initial command when requesting authentication
		$this->sendRequest("STARTTLS ". Mailer::RN );
		$this->setResponse('tls');

		//We should say EHLO as initial command when requesting authentication
		$this->sendRequest("EHLO $host ". Mailer::RN);
		$this->setResponse('second_ehlo_response');

		//Authentication request
		$this->sendRequest("AUTH LOGIN ". Mailer::RN);
		$this->setResponse('auth_request');

		//Both username and password have to be encoded by base64_encode
		$username = base64_encode($this->mailer->getUsername());
		$this->sendRequest($username . Mailer::RN);
		$this->setResponse('auth_username' );

		//Both username and password have to be encoded by base64_encode
		$password = base64_encode($this->mailer->getPassword());
		$this->sendRequest($password . Mailer::RN);
		$this->setResponse('auth_password');

		//Only email adress goes here, donot enter your name
		$sender = $this->mailer->getSender();
		$this->sendRequest("MAIL FROM:<$sender>". Mailer::RN);
		$this->setResponse('mailfrom_response' );

		//Only email adress goes here, donot enter your name
		$recipient = $this->mailer->getRecipient();
		$this->sendRequest("RCPT TO:<$recipient>". Mailer::RN);
		$this->setResponse('mailto_response' );
		
		$this->sendRequest("DATA". Mailer::RN);
		$this->setResponse('DATA');

		//Send request headers
		$header = $this->mailer->getResolvedHeader();
		$header.= Mailer::RN;
		$this->sendRequest($header);
		$this->setResponse('header_response');

		//Send email subject
		$subject = $this->mailer->getSubject();
		$subject.= Mailer::RN;
		$this->sendRequest("Subject: ". $subject);
		$this->setResponse('subject_response');

		//Send email message
		$message = $this->mailer->getResolvedMessage();
		$message.= Mailer::RN;
		$this->sendRequest($message);
		$this->setResponse('message_response');

		//Close the connection
		$this->sendRequest(".". Mailer::RN);
		$this->setResponse('close_response');

		//Quit from the socket connection
		$this->sendRequest("QUIT ".Mailer::RN);
		$this->setResponse('QUIT');

		$response = substr($this->connection, 0, 3);
		$this->setResponse('close_code_response', $response);
	}

	/**
	 * @inheritdoc
	 * See \Repository\Component\Contrracts\Mail\SmtpInterface::isSent
	 */	
	public function isSent()
	{
		//Check whether close_code_response key existed one
		if (array_key_exists('close_code_response', $this->getResponses())) {
			$response = $this->getResponse('close_code_response');
			//when 2355 or 221 it's means success email sent
			return ( ($response === '221') || ($response === '235') || ($response === '250') )? true : false ;
		}
	}

	/**
	 * Set response giveen by socket request
	 * 
	 * @param string $key The key for response
	 * @param string $response The response message
	 * @param int $buffer The lenght too read response
	 * 
	 * @return \Repository\Component\Contracts\Mail\IMailConnection
	 */
	public function setResponse($key, $response = null, $buffer = 4096)
	{
		if (null === $response)
			$response = fgets($this->connection, $buffer);

		$this->responses[$key] = $response;
		
		return $this;
	}

	/**
	 * @inheritdoc
	 * See \Repository\Component\Contrracts\Mail\SmtpInterface::getResponses
	 */	
	public function getResponses()
	{
		$responses = $this->responses;
		
		return (array) $responses;
	}

	/**
	 * @inheritdoc
	 * See \Repository\Component\Contrracts\Mail\SmtpInterface::getResponse
	 */	
	public function getResponse($key)
	{
		$response = $this->responses[$key];
		
		return $response;
	}
}