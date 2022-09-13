<?php
namespace Repository\component\Session\Handlers;

use Repository\Component\Contracts\Encryption\EncryptionInterface;
use Repository\Component\Session\Exception\SessionException;
use SessionHandlerInterface;

/**
 * Abstract Session Handler.
 * 
 * @package	  \Repository\Component\Session
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
abstract class AbstractSessionHandler implements SessionHandlerInterface
{
	/**
	 * Session target storage
	 * @var string $target
	 */
	public $target;

	/**
	 * Session target prefix
	 * @var string $prefix
	 */
	public $prefix = 'repository_';

	/**
	 * The session encrypter
	 * @var \Repository\Component\Contracts\Encryption\EncryptionInterface $encrypter
	 */	
	private $encrypter;

	/**
	 * Whether or not session use encryption
	 * @var bool $useEncryption
	 */
	private $useEncryption = false;

	/**
	 * {@inheritdoc}
	 */
	public function read($id)
	{
		return $this->unserialize($this->handleRead($id));
	}

	/**
	 * {@inheritdoc}
	 */
	public function write($id, $data)
	{
		$this->handleWrite($id, $this->serialize($data));
	}
	
	/**
	 * Handle read logic to the session handler
	 * 
	 * @param string $id The session id
	 * 
	 * @return mixed
	 */
	abstract function handleRead($id);

	/**
	 * Handle write logic to the session handler
	 * 
	 * @param string $id The session id
	 * @param mixed $data The session data
	 * 
	 * @return void
	 */	
	abstract function handleWrite($id, $data);

	/**
	 * Set encryption handler used for session encryption
	 * 
	 * @param \Repository\Component\Contracts\Encryption\EncryptionInterface $encrypter
	 * 
	 * @return void
	 */	
	public function setEncryptionHandler(EncryptionInterface $encrypter)
	{
		$this->encrypter = $encrypter;
	}

	/**
	 * Set session prefix
	 * 
	 * @param string $prefix
	 * 
	 * @return void
	 */
	public function setPrefix(string $prefix)
	{
		$this->prefix = $prefix;
	}

	/**
	 * Weather or not use session encryption
	 * 
	 * @param bool $uses
	 * 
	 * @return void
	 */	
	public function useEncryption(bool $uses = false)
	{
		$this->useEncryption = $uses;
	}

	/**
	 * Serialize the given session data
	 * 
	 * @param mixed $data
	 * 
	 * @throw \Repository\Component\Session\Exception\SessionException
	 * 
	 * @return Serialized session data when session encryption is enabled
	 * Plain data otherwise
	 */	
	public function serialize($data)
	{
		if ($this->useEncryption) {
			if ($this->encrypter === null) {
				throw new SessionException("The session encrypter not set");
			}
			
			try {
				$data = $this->encrypter->encrypt($data);
				return $data;
			} catch (\Exception $ex) {
				return $data;
			}
		}
		
		return $data;
	}

	/**
	 * Unserialize the given session data
	 * 
	 * @param mixed $data
	 * 
	 * @throw \Repository\Component\Session\Exception\SessionException
	 * 
	 * @return Serialized session data when session encryption is enabled
	 * Plain data otherwise
	 */	
	public function unserialize($data)
	{
		if ($this->useEncryption) {
			if ($this->encrypter === null) {
				throw new SessionException("The session encrypter not set");
			}
			
			try {
				$data = $this->encrypter->decrypt($data);
				return $data;
			} catch (\Exception $ex) {
				return $data;
			}
		}
		
		return $data;
	}
}