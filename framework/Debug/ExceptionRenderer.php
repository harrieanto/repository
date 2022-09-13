<?php
namespace Repository\Component\Debug;

use Throwable;
use Repository\Component\Config\Config;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Repository\Component\View\ViewFactory;
use Repository\Component\Collection\Collection;
use Repository\Component\Http\Exception\HttpException;
use Repository\Component\Http\Exception\NotFoundHttpException;
use Repository\Component\Filesystem\Filesystem as Fs;
use Repository\Component\Contracts\Filesystem\FilesystemInterface;
use Repository\Component\Contracts\Debug\ExceptionRendererInterface;
use Repository\Component\Contracts\Container\ContainerInterface;
use Repository\Component\Console\Responses\Formatter\OutputFormatter;
use Repository\Component\Console\Responses\Formatter\Table;
use Repository\Component\Console\Responses\ConsoleResponse;

/**
 * Handle Exception to the Response Handler.
 *
 * @package	  \Repository\Component\Debug
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ExceptionRenderer implements ExceptionRendererInterface
{
	/** The html response view path **/
	const VIEW_PATH = 'error' . DS . 'exception';

	/** The html view extension **/
	const VIEW_EXTENSION = '.php';

	/** The friendly message when debug is turn off **/
	const FRIENDLY_MESSAGE = 'Oops...Something Went Wrong!';

	private $app;

	/**
	 * Filesystem instance
	 * @var \Repository\Component\Contracts\Filesystem\FsInterface $fs
	 */
	private $fs;

	/**
	 * Request instance
	 * @var \Psr\Http\Message\RequestInterface $request
	 */
	private $request;

	/**
	 * Shared exception variable to the view response
	 * @var string $viewVars
	 */
	private static $viewVars = 'exception';

	/**
	 * Resolved exception stacktrace
	 * @var \Repository\Component\collection\Collection $stacktrace
	 */
	private $stacktrace;

	/**
	 * Exception instance
	 * @var \Throwable $exception
	 */	
	private $exception;

	/**
	 * Status code header
	 * @var int $statusCode
	 */
	private $statusCode = 500;

	private $debug;
	
	/**
	 * @param \Repository\Component\Contracts\Filesystem\FsInterface $fs
	 * @param \Psr\Http\Message\RequestInterface $request
	 */
	public function __construct(
		ContainerInterface $app, 
		FilesystemInterface $fs, 
		RequestInterface $request, 
		ResponseInterface $response)
	{
		$this->app = $app;
		$this->fs = $fs;
		$this->request = $request;
		$this->response = $response;
	}

	/**
	 * Handle received exception to the specific view responses
	 * 
	 * @param \Throwable $exception
	 * 
	 * @return void
	 */
	public function handle(Throwable $exception, $debug = true)
	{
		//Fetch content wheres exception occured
		$exceptionContent = $this->fs->getContent($exception->getFile());
		//Get total line of fetched content by newline
		$totalLine = count($lines = explode("\n", $exceptionContent));
		$exceptions = array_combine(
			range(1, $totalLine), 
			array_values($lines)
		);
		//Resolve pointer indicator
		$pointer = $exception->getLine() - 5;
		$lastPointer = $exception->getLine() + 5;
		
		//Resolve initial pointer
		if ($pointer < 1) $pointer  = 1;
		//Resolve last pointer
		if ($lastPointer > $totalLine) $lastPointer = $totalLine;
		
		$content = Collection::make(array());

		for ($i = $pointer; $i <= $lastPointer; $i++) {
			if ($exception->getLine() === $i) {
				$content->add($i, $exceptions[$i]);
			} else {
				$content->add($i, $exceptions[$i]);
			}
		}
		
		$this->debug = $debug;
		$this->setExceptionInstance($exception);
		$this->setResolvedExceptionStacktrace($content);
		$this->convert($exception);
	}
	
	/**
	 * Set exception instance
	 * 
	 * @param \Throwable $exception
	 * 
	 * @return void
	 */
	private function setExceptionInstance($exception)
	{
		$this->exception = $exception;
	}

	/**
	 * Set resolved exception stacktrace
	 * 
	 * @param \Repository\Component\Collection\Collection $item
	 * 
	 * @return void
	 */
	private function setResolvedExceptionStacktrace(Collection $item)
	{
		$this->stacktrace = $item;
	}

	/**
	 * Convert received exception to the specific view response
	 * 
	 * @param \Throwable $exception
	 * 
	 * @return void
	 */	
	public function convert(Throwable $exception)
	{
		$statusCode = 500;

		if ($exception instanceof NotFoundHttpException) {
			$statusCode = $exception->getStatusCode();
		} else if ($exception instanceof HttpException) {
			$statusCode = $exception->getStatusCode();
		}

		$this->statusCode = $statusCode;
		
		$whoops = new \Whoops\Run;
		$whoops->sendHttpCode($this->statusCode);

		if (!$this->debug) {
			$this->handleFriendlyErrorMessage($whoops);
		} else {
			$this->handleDebugException($whoops);
		}
	}

	private function handleFriendlyErrorMessage($whoops)
	{
		if ($this->request->isAjax() || $this->request->isJson()) {
			$message = app_config(sprintf('debug.messages.%s', $this->statusCode));
			
			if ($this->exception instanceof NotFoundHttpException) {
				$message = $this->exception->getMessage();
			}

			if (!$message) {
				$message = self::FRIENDLY_MESSAGE;
			}

			$messages['error'] = array(
				'message' => $message
			);

			echo $this->response->json($messages, $this->statusCode);
			$this->response->sendHeaders();
			exit;
		} else {
			$this->abort($this->exception);
		}
	}

	/**
	 * Abort exception with user friendly message
	 * This method will automatically called when production environment is enabled
	 * 
	 * @param \Throwable $exception
	 * 
	 * @throw \Repository\Component\Http\Exception\HttpException
	 * 
	 * @return void
	 */	
	public function abort($exception)
	{
		if ($exception instanceof HttpException) {
			return $this->app->abort(
				$exception->getStatusCode(), 
				$exception->getStatusText()
			);
		}

		$message = 'Internal Server Error';

		if ($this->request->getRequestMethod() === 'options') {
			$this->app->abort(204, 'No Content');
		}

		$this->app->abort($this->statusCode, '');
	}

	private function handleDebugException($whoops)
	{
		if ($this->request->isXml()) {
			$this->xml($whoops);
		} else if ($this->request->isAjax() || $this->request->isJson()) {
			$this->json($whoops);
		} else if ($this->request->isPlain()) {
			$this->plain($whoops);
		} else if ($this->request->isRunningInConsole()) {
			$this->console();
		} else {
			$this->html($whoops);
		}
	}

	/**
	 * Convert received exception to the json format
	 * 
	 * @return void
	 */	
	public function json(\Whoops\RunInterface $whoops)
	{
		$handler = new \Whoops\Handler\JsonResponseHandler;
		$whoops->prependHandler($handler);
		$whoops->handleException($this->exception);
	}

	/**
	 * Convert received exception to the json format
	 * 
	 * @return void
	 */	
	public function plain(\Whoops\RunInterface $whoops)
	{
		$handler = new \Whoops\Handler\PlainTextHandler;
		$whoops->prependHandler($handler);
		$whoops->handleException($this->exception);
	}

	/**
	 * Convert received exception to the json format
	 * 
	 * @return void
	 */	
	public function xml(\Whoops\RunInterface $whoops)
	{
		$handler = new \Whoops\Handler\XmlResponseHandler;
		$whoops->prependHandler($handler);
		$whoops->handleException($this->exception);
	}

	/**
	 * Convert received exception to the console
	 * 
	 * @return void
	 */	
	public function console()
	{
		$ex = $this->getExceptionAsArray($this->exception);
		$console = new ConsoleResponse(new OutputFormatter);

		$data = array(
			'Error occured' => array(
				$ex['instanceof']
			), 
			'File' => array(
				$ex['pathfile']
			), 
			'Line' => array(
				$ex['line']
			), 
			'Message' => array(
				$ex['message']
			), 
		);

		$table = new Table($data, $console);
		$table->renderTable();
	}

	/**
	 * Convert received exception to the html template
	 * 
	 * @param \Throwable $exception
	 * 
	 * @return void
	 */		
	public function html(\Whoops\RunInterface $whoops)
	{
		$handler = new \Whoops\Handler\PrettyPageHandler;
		$whoops->prependHandler($handler);
		$whoops->handleException($this->exception);
	}

	/**
	 * Convert received exception to the readable array format
	 * 
	 * @param \Throwable $exception
	 * 
	 * @return void
	 */
	public function getExceptionAsArray(Throwable $exception)
	{
		$collections = Collection::make(array());
		$collections->add('instanceof', get_class($exception));
		$collections->add('message', $exception->getMessage());
		$collections->add('pathfile', $exception->getFile());
		$collections->add('filename', $this->resolveFilename($exception));
		$collections->add('line', $exception->getLine());
		$collections->add('contents', $this->stacktrace->all());
		$collections->add('traces', $this->getTraceAsArray($exception));
		$collections->add('params', $this->request->getServerParams());
		
		return $collections;
	}

	/**
	 * Resolve filname from the occured error/excepption
	 * 
	 * @param \Repository\Component\Contracts\Container\ContainerInterface $app
	 * @param \Throwable $exception
	 * 
	 * @return string The error occured filename
	 */	
	private function resolveFilename(Throwable $exception)
	{
		return $this->request->getLastPath($exception->getFile(), DS);
	}
	
	/**
	 * Get exception string trace as array
	 * 
	 * @param \Throwable $exception
	 * 
	 * @return array Exception traces
	 */	
	private function getTraceAsArray(Throwable $exception)
	{
		$trace = $exception->getTraceAsString();

		if (preg_match_all("/\#[0-9]+\s+/", $trace, $matches)) {
			$excludedList = $matches[0];
		}

		foreach ($excludedList as $excluded) {
			$trace = preg_replace("/$excluded/", '#', $trace);
		}
		
		$traces = explode('#', $trace);

		return $traces;
	}

	private function generateStackTraces()
	{
		$backtraces = array();

		foreach(debug_backtrace() as $key => $stacks) {
			$backtraces[] = $stacks['file'];
		}

		return $backtraces;
	}
}