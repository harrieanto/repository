<?php
namespace Repository\Component\Session\Handlers;

/**
 * File Session Handler.
 * 
 * @package	  \Repository\Component\Session
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class FileSessionHandler extends AbstractSessionHandler
{	
	/**
	 * {@inheritdoc}
	 */
	public function __construct($target)
	{
		$this->target = $target;
	}

	/**
	 * {@inheritdoc}
	 */
	public function open($savePath, $sessionName)
	{
		if (!is_dir($this->target)){
			return mkdir($this->target,  0755);
		}

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
		$target = $this->target . '/' . $this->prefix . $id;

		if (file_exists($target)) {
			return file_get_contents($target);
		}
		
		return '';
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Session\Handlers\AbstractSessionHandler::handleWrite
	 */
	public function handleWrite($id, $data)
	{
		$target = $this->target . '/' . $this->prefix . $id;
		
		return file_put_contents($target, $data, LOCK_EX) !== false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function destroy($id)
	{
		$target = $this->target . '/' . $this->prefix . $id;

		if (file_exists($target)) {
			unlink($target);
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function gc($maxlifetime)
	{
		$target = $this->target . '/' . $this->prefix. "*";

		foreach (glob($target) as $fileName) {
			if (filemtime($fileName) + $maxlifetime < time() && file_exists($fileName)){
				unlink($fileName);
			}
		}

		return true;
	}
}