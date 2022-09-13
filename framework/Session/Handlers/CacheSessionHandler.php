<?php
namespace Repository\Component\Session\Handlers;

use Repository\Component\Cache\Repository;

/** 
 * Cache Session Handler.
 * 
 * @package	  \Repository\Component\Session
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class CacheSessionHandler extends AbstractSessionHandler
{
	/**
	 * @var $cache The cache to use
	 */
	private $cache = null;

	/**
	 * @var $lifetime The lifetime in seconds
	 */
	private $lifetime = 0;

	/**
	 * @param $cache The cache to use
	 * @param $lifetime The lifetime in seconds
	 */
	public function __construct(Repository $cache, int $lifetime)
	{
		$this->cache = $cache;
		$this->lifetime = $lifetime;
	}

	/**
	 * {@inheritdoc}
	 */
	public function open($savePath, $sessionName)
	{
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function close()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Session\Handlers\AbstractSessionHandler::handleRead
	 */
	public function handleRead($id)
	{
		return $this->cache->get($id, '');
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Session\Handlers\AbstractSessionHandler::handleWrite
	 */
	public function handleWrite($id, $data)
	{
		$this->cache->put($id, $data, $this->lifetime);

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function destroy($id)
	{
		$this->cache->forget($id);

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function gc($maxlifetime)
	{
		return true;
	}
}