<?php
namespace Repository\Component\Session;

use SessionHandlerInterface;
use Repository\Component\Contracts\Session\SessionInterface;
use Repository\Component\Contracts\Container\Containerinterface;

/**
 * Session Factory.
 * 
 * @package	  \Repository\Component\Session
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class SessionFactory
{
	/**
	 * @var \Repository\Component\Contracts\Container\Containerinterface $app
	 */
	private $app;

	/**
	 * @var \Repository\Component\Contracts\Session\SessionInterface $session
	 */
	private $session;

	/**
	 * @var \SessionHandlerInterface $handler
	 */
	private $handler;

	/**
	 * @param \Repository\Component\Contracts\Container\Containerinterface $app
	 * @param \Repository\Component\Contracts\Session\SessionInterface $session
	 * @param \SessionHandlerInterface $handler
	 */
	public function __construct(
		ContainerInterface $app, 
		SessionInterface $session, 
		SessionHandlerInterface $handler)
	{
		$this->app = $app;
		$this->session = $session;
		$this->handler = $handler;
	}

	public function gc()
	{
		//
	}
	
	/**
	 * Start session according to the session handler
	 * 
	 * @return void
	 */
	public function startSession()
	{
		$this->gc();
		$cookies = $this->app['request']->getCookieParams();

		if (!isset($cookies[$this->session->getName()])) {
			return false;
		}

		$id = $cookies[$this->session->getName()];

		$this->session->setId($id);
		$this->handler->open(null, $id);
		$items = unserialize($this->handler->read($id));

		if (!$items) {
			$items = array();
		}

		$this->session->start($items);
	}

	/**
	 * Write session according to the session handler
	 * 
	 * @return void
	 */
	public function writeSession()
	{
		$this->session->getFlash()->deleteOldFlashKeys();
		$data = serialize($this->session->getAll());
		$this->handler->write($this->session->getId(), $data);
	}
}