<?php
namespace Repository\Component\Session;

use Repository\Component\Contracts\Session\SessionInterface;

/**
 * Session Flash.
 * 
 * @package	  \Repository\Component\Session
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Flash
{
	/** The default old flash key **/
	const DEFAULT_OLD_FLASH_KEY = 'repository_old_session_flash';

	/** The default new flash key **/
	const DEFAULT_NEW_FLASH_KEY = 'repository_new_session_flash';
	
	/**
	 * The session instance
	 * @var \Repository\Component\Contracts\Session\SessionInterface
	 */
	private $session;

	/**
	 * @param \Repository\Component\Contracts\Session\SessionInterface
	 */
	public function __construct(SessionInterface $session)
	{
		$this->session = $session;
	}

	/**
	 * Set session flash
	 * Session flash will deleted on the next request
	 * 
	 * @param $key The key of session item
	 * @param $value The value of session item
	 * 
	 * @return void
	 */	
	public function set($key, $item)
	{
		$this->session->set($key, $item);
		$newFlashKeys = $this->getNewFlashKeys();
		$newFlashKeys[] = $key;
		$this->session->set(self::DEFAULT_NEW_FLASH_KEY, $newFlashKeys);
		$oldFlashKeys = $this->getOldFlashKeys();
		
		if (in_array($key, $oldFlashKeys)) {
			unset($oldFlashKeys[$key]);
		}

		$this->session->set(self::DEFAULT_OLD_FLASH_KEY, $oldFlashKeys);
	}

	/**
	 * Reflash to extend the lifetime of registered session flash
	 * 
	 * @return void
	 */
	public function reflash()
	{
		$oldFlashKeys = $this->getOldFlashKeys();
		$newFlashKeys = $this->getNewFlashKeys();
		$newFlashKeys = array_merge($oldFlashKeys, $newFlashKeys);
		
		$this->session->set(self::DEFAULT_NEW_FLASH_KEY, $newFlashKeys);
		$this->session->set(self::DEFAULT_OLD_FLASH_KEY, array());
	}

	/**
	 * Delete old available session flash key
	 * 
	 * @return void
	 */	
	public function deleteOldFlashKeys()
	{
		$oldFlashKeys = $this->getOldFlashKeys();

		foreach ($oldFlashKeys as $oldFlashKey) {
			$this->session->forget($oldFlashKey);
		}
		
		$this->session->set(self::DEFAULT_OLD_FLASH_KEY, $this->getNewFlashKeys());
		$this->session->set(self::DEFAULT_NEW_FLASH_KEY, array());
	}

	/**
	 * Get new flash keys
	 * 
	 * @return array The list of registered session flash keys
	 */	
	public function getNewFlashKeys()
	{
		$newFlashKeys = $this->session->get(self::DEFAULT_NEW_FLASH_KEY, array());
		
		return $newFlashKeys;
	}

	/**
	 * Get old flash keys
	 * 
	 * @return array The list of registered session flash keys
	 */	
	public function getOldFlashKeys()
	{
		$oldFlashKeys = $this->session->get(self::DEFAULT_OLD_FLASH_KEY, array());
		
		return $oldFlashKeys;
	}
}