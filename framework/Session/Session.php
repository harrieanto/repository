<?php
namespace Repository\Component\Session;

use ArrayAccess;
use RuntimeException;
use InvalidArgumentException;
use Repository\Component\Contracts\Session\SessionInterface;

/**
 * Session Transaction.
 * 
 * @package	  \Repository\Component\Session
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Session implements SessionInterface, ArrayAccess
{
	/**
	 * Whether or not session has started
	 * @var bool $started
	 */
	private $started = false;

	/**
	 * Session items collection
	 * @var array $items
	 */
	private $items = array();

	/**
	 * Session name
	 * @var string $name
	 */
	private $name;

	/**
	 * Session id
	 * @var string $id
	 */
	private $id = '';

	/**
	 * @var \Repository\Component\Session\IdGenerator $idGenerator
	 */
	private $idGenerator;
	
	/**
	 * Initialize session id for the first time
	 * 
	 * @param null|string $id
	 * @param null|string $name
	 */	
	public function __construct($id = null, $name = null)
	{
		$this->idGenerator = new IdGenerator;
		$this->flash = new Flash($this);
		
		if ($id && $name !== null) {
			$this->setId($id);
			$this->setName($name);
		}
	}

	/**
	 * Start session
	 * 
	 * @param array $items The items that want add to the initial session
	 * 
	 * @return bool True determine if the session has started, false otherwise
	 */	
	public function start(array $items = array())
	{
		$this->setMany($items);
		$this->started = true;
		
		return $this->started;
	}

	/**
	 * Determine if the session has started
	 * 
	 * @return bool True determine if the session has started, false otherwise
	 */
	public function hasStarted()
	{
		if ($this->started) return true;
		
		return false;
	}

	/**
	 * Set item to the started session
	 * 
	 * @param string|int $key The key of item
	 * @param mixed $item The value of the item
	 * 
	 * @return void
	 */	
	public function set($key, $item)
	{
		$this->items[$key] = $item;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Session\\Flash::flash()
	 * 
	 * @return void
	 */
	public function flash($key, $item)
	{
		$this->flash->set($key, $item);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Session\\Flash::reflash()
	 * 
	 * @return void
	 */	
	public function reflash()
	{
		$this->flash->reflash();
	}

	/**
	 * Get session flash instance
	 * 
	 * @return \Repository\Component\Session\Flash
	 */
	public function getFlash()
	{
		return $this->flash;
	}

	/**
	 * Set many item to the current started session
	 * 
	 * @param array $items
	 * 
	 * @return void
	 */	
	public function setMany($items)
	{
		$items = array_merge($items, $this->items);
		$this->items = $items;
	}

	/**
	 * Get item by the given key
	 * 
	 * @param int|string $key
	 * @param mixed $default The default value when the value of the given key was not found
	 * 
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		if ($this->has($key)) {
			return $this->items[$key];
		}
		
		return $default;
	}

	/**
	 * Get all item from the current session
	 * 
	 * @return array
	 */
	public function getAll()
	{
		return $this->items;
	}

	/**
	 * Set session id
	 * 
	 * @param string $id
	 * 
	 * @return void
	 */	
	public function setId(string $id)
	{
		if (!$this->idGenerator->isValid($id)) {
			throw new RuntimeException("The session id [$id] is invalid");
		}
		
		$this->id = $id;
	}

	/**
	 * Get session id
	 * 
	 * @return string
	 */		
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Regenerate fresh session id
	 * 
	 * @return void
	 */		
	public function regenerateId()
	{
		$id = $this->idGenerator->generate();
		
		$this->setId($id);
	}

	/**
	 * Set session name
	 * 
	 * @param string $name The name of session
	 * 
	 * @return void
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Get session name
	 * 
	 * @return string
	 */			
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Determine if the given item key has a value
	 * 
	 * @param int|string $key The key of the session items
	 * 
	 * @return bool
	 */
	public function has($key)
	{
		return isset($this->items[$key]);
	}

	/**
	 * Delete session item by the given key
	 * 
	 * @param int|string $key The key of the session items
	 * 
	 * @return void
	 */
	public function forget($key)
	{
		if ($this->has($key)) {
			unset($this->items[$key]);
		}
	}

	/**
	 * Delete all session items
	 * 
	 * @return void
	 */
	public function flush()
	{
		$this->items = [];
	}
	
	/**
	 * @{inheritdoc}
	 */	
	public function offsetExists($key)
	{
		return $this->has($key);
	}

	/**
	 * @{inheritdoc}
	 */
	public function offsetUnset($key)
	{
		$this->forget($key);
	}

	/**
	 * @{inheritdoc}
	 */	
	public function offsetSet($key, $value)
	{
		if ($key === null) {
			throw new InvalidArgumentException("Session key identifier can't null");
		}
		
		$this->set($key, $value);
	}

	/**
	 * @{inheritdoc}
	 */	
	public function offsetGet($key)
	{
		return $this->get($key);
	}
}
