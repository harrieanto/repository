<?php
namespace Repository\Component\Debug;

use ErrorException;
use Psr\Log\LoggerInterface;
use Repository\Component\Config\Config;
use Repository\Component\Contracts\Debug\ErrorInterface;
use Repository\Component\Contracts\Debug\ExceptionInterface;

/**
 * Error Handler.
 *
 * @package	  \Repository\Component\Debug
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ErrorHandler implements ErrorInterface
{
	/** @var array The common PHP error handler types **/
	const ERROR_TYPES = array(
		E_ERROR, 
		E_WARNING, 
		E_NOTICE, 
		E_PARSE, 
		E_CORE_ERROR, 
		E_CORE_WARNING, 
		E_COMPILE_ERROR, 
		E_COMPILE_WARNING, 
		E_USER_ERROR, 
		E_USER_WARNING, 
		E_USER_NOTICE, 
		E_STRICT, 
		E_RECOVERABLE_ERROR, 
		E_DEPRECATED, 
		E_USER_DEPRECATED
	);

	/** @var array The common PHP shutdown error handler types **/
	const SHUTDOWN_ERROR_TYPES = array(
		E_ERROR, 
		E_PARSE, 
		E_CORE_ERROR, 
		E_COMPILE_ERROR, 
		E_RECOVERABLE_ERROR, 
		E_CORE_WARNING, 
		E_COMPILE_WARNING, 
		E_USER_ERROR
	);

	/**
	 * @param \Repository\Component\Contracts\Debug\ExceptionInterface $exception
	 * @param \Psr\Log\LoggerInterface $logger
	 */
	public function __construct(
		ExceptionInterface $exception, 
		LoggerInterface $logger)
	{
		$this->exception = $exception;
		$this->logger = $logger;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Debug\ErrorInterface::handle
	 */	
	public function handle(
		int $level, 
		string $message, 
		string $file = '', 
		int $line = 0, 
		array $context = [])
	{
		$status = $message . " at file " . $file . " on line " . $line;
		$exception = new ThrowableFatalException($message, $level, 0, $file, $line);

		if (!is_null($level) && in_array($level, self::ERROR_TYPES)) {
			$this->logger->log('error', $status, $context);
			$this->exception->handle($exception);
		}
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Debug\ErrorInterface::shutdown
	 */
	public function shutdown()
	{
		$errors = error_get_last();

		if (!is_null($errors) && in_array($errors['type'], self::SHUTDOWN_ERROR_TYPES)) {
			$this->handle(
				$errors['type'], 
				$errors['message'], 
				$errors['file'], 
				$errors['line']
			);
		}
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Debug\ErrorInterface::register
	 */
	public function register()
	{
		ini_set('display_errors', 'off');
		error_reporting(-1);
		set_error_handler([$this, 'handle']);
		register_shutdown_function([$this, 'shutdown']);
	}
}