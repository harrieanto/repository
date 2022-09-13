<?php
namespace Repository\Component\Debug;

use Exception;
use Repository\Component\Config\Config as GlobalConfig;
use Repository\Component\Http\Exception\HttpException;
use Repository\Component\Contracts\Debug\ExceptionInterface;
use Repository\Component\Http\Exception\NotFoundHttpException;
use Repository\Component\Contracts\Debug\ExceptionRendererInterface;

/**
 * Exception Handler.
 *
 * @package	  \Repository\Component\Debug
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ExceptionHandler implements ExceptionInterface
{
	/**
	 * @var Repository\Component\Debug\Config $config
	 */
	private $config;

	/**
	 * @var Repository\Component\Contracts\Debug\ExceptionRendererInterface $exceptionRenderer
	 */
	private $exceptionRenderer;

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Debug\ExceptionRendererInterface
	 */	
	public function __construct(Config $config, ExceptionRendererInterface $exRenderer)
	{
		$this->config = $config;
		$this->exceptionRenderer = $exRenderer;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Debug\ExceptionInterface::handle()
	 */
	public function handle($exception)
	{
		$debug = $this->config->isThrowableAlertEnable();
		$this->exceptionRenderer->handle($exception, $debug);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contract\Debug\DebugInterface::register()
	 */
	public function register()
	{
		set_exception_handler([$this, 'handle']);
	}
}