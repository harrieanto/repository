<?php
namespace Repository\Component\Contracts\Session;

/**
 * Session Interface.
 * 
 * @package	 \Repository\Component\Contracts\Session
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface SessionInterface
{
	/**
	 * Start session
	 * 
	 * @param array $items The items that want add to the initial session
	 * 
	 * @return bool True determine if the session has started, false otherwise
	 */	
	public function start(array $items = array());

	/**
	 * Determine if the session has started
	 * 
	 * @return bool True determine if the session has started, false otherwise
	 */
	public function hasStarted();

	/**
	 * Set item to the started session
	 * 
	 * @param string|int $key The key of item
	 * @param mixed $item The value of the item
	 * 
	 * @return void
	 */	
	public function set($key, $item);

	/**
	 * Set many item to the current started session
	 * 
	 * @param array $items
	 * 
	 * @return void
	 */		
	public function setMany($items);

	/**
	 * Get item by the given key
	 * 
	 * @param int|string $key
	 * @param mixed $default The default value when the value of the given key was not found
	 * 
	 * @return mixed
	 */
	public function get($key, $default = null);

	/**
	 * Get all item from the current session
	 * 
	 * @return array
	 */
	public function getAll();

	/**
	 * Set session id
	 * 
	 * @param string $id
	 * 
	 * @return void
	 */	
	public function setId(string $id);

	/**
	 * Get session id
	 * 
	 * @return string
	 */		
	public function getId();

	/**
	 * Regenerate fresh session id
	 * 
	 * @return void
	 */		
	public function regenerateId();

	/**
	 * Set session name
	 * 
	 * @param string $name The name of session
	 * 
	 * @return void
	 */
	public function setName($name);

	/**
	 * Get session name
	 * 
	 * @return string
	 */			
	public function getName();

	/**
	 * Determine if the given item key has a value
	 * 
	 * @param int|string $key The key of the session items
	 * 
	 * @return bool
	 */
	public function has($key);

	/**
	 * Delete session item by the given key
	 * 
	 * @param int|string $key The key of the session items
	 * 
	 * @return void
	 */
	public function forget($key);

	/**
	 * Delete all session items
	 * 
	 * @return void
	 */
	public function flush();
}