<?php
namespace Repository\Component\Foundation;

use Repository\Component\Support\Str;
use Repository\Component\Config\Config;
use Repository\Component\Config\FileLoader;
use Repository\Component\Http\HttpServiceProvider;
use Repository\Component\Log\LoggerServiceProvider;
use Repository\Component\Event\EventServiceProvider;
use Repository\Component\Http\Exception\HttpException;
use Repository\Component\Filesystem\FilesystemServiceProvider;
use Repository\Component\Http\Exception\NotFoundHttpException;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * The Application Repository Faramework.
 *
 * @package	  \Repository\Component\Foundation
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Application extends DeferredProvider
{
	/**
	 * The Repository framework version.
	 * @var string
	 */
	const VERSION = '1.0';
	
	/**
	 * The user friendly not found message
	 * @var string $notFound
	 */
	protected $notFound;

	/**
	 * All of the registered service providers.
	 * @var array
	 */
	protected $serviceProviders = array();

	/**
	 * The names of the loaded service providers.
	 * @var array
	 */
	protected $loadedProviders = array();

    /**
     * The prefixes of absolute cache paths for use during normalization.
     *
     * @var string[]
     */
    protected $absoluteCachePathPrefixes = ['/', '\\'];

	/**
	 * The app instance.
	 * @var \Repository\Component\Foundation\Application
	 */	
	private static $instance;

	/**
	 * Create new Repositoriey application
	 */
	public function __construct($basePath = null)
	{
        if ($basePath) {
            $this->setBasePath($basePath);
        }

		$this->registerBaseBindings();
		$this->registerBaseServiceProviders();

		static::$instance = $this;
	}

    /**
     * Set the base path for the application.
     *
     * @param  string  $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');

        $this->bindPathsInContainer();

        return $this;
    }

    /**
     * Bind all of the application paths in the container.
     *
     * @return void
     */
    protected function bindPathsInContainer()
    {
        $this->instance('path.base', $this->basePath());
    }

    /**
     * Get the path to the bootstrap directory.
     *
     * @param  string  $path Optionally, a path to append to the bootstrap path
     * @return string
     */
    public function bootstrapPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'bootstrap'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Normalize a relative or absolute path to a cache file.
     *
     * @param  string  $key
     * @param  string  $default
     * @return string
     */
    protected function normalizeCachePath($key, $default)
    {
        if (is_null($env = app_env($key))) {
            return $this->bootstrapPath($default);
        }

        return Str::startsWith($env, $this->absoluteCachePathPrefixes)
                ? $env
                : $this->basePath($env);
    }

    /**
     * Get the base path of the framework installation.
     *
     * @param  string  $path Optionally, a path to append to the base path
     * @return string
     */
    public function basePath($path = '')
    {
        return $this->basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

	/**
	 * Get local language
	 * @return string
	 */
	public function getLocale()
	{
		return \Repository\Component\Language\Lang::lang();
	}

	/**
	 * Get app instance
	 * @return \Repository\Component\Foundation\Application
	 */	
	public static function getInstance()
	{
		return static::$instance;
	}

	/**
	 * Register the basic binding into container
	 * 
	 * @return void
	 */
	public function registerBaseBindings()
	{
		$this->instance(ContainerInterface::class, $this);
	}

	/**
	 * Register all of the base service providers
	 * 
	 * @return void
	 */
	public function registerBaseServiceProviders()
	{
		foreach (array('Event', 'Fs', 'Http', 'Logger') as $name) {
			$this->{"register{$name}Provider"}();
		}
	}

	/**
	 * Register Event service provider
	 * 
	 * @return void
	 */
	public function registerEventProvider()
	{
		$this->register(new EventServiceProvider($this));
	}

	/**
	 * Register Filesystem service provider
	 * 
	 * @return void
	 */
	public function registerFsProvider()
	{
		$this->register(new FilesystemServiceProvider($this));
	}

	/**
	 * Register Filesystem service provider
	 * 
	 * @return void
	 */
	public function registerHttpProvider()
	{
		$this->register(new HttpServiceProvider($this));
	}

	/**
	 * Register Logger service provider
	 * 
	 * @return void
	 */
	public function registerLoggerProvider()
	{
		$this->register(new LoggerServiceProvider($this));
	}

	/**
	 * Register a service provider with the application
	 * 
	 * @param \Repository\Component\Support\ServiceProvider|string $provider
	 * @param array $options
	 * 
	 * @return void
	 */
	public function register($provider,  $options = array())
	{
		if ($registered = $this->getRegistered($provider)) {
			return $registered;
		}

		if (is_string($provider)) {
			$provider = $this->resolveProviderClass($provider);
		}

		//Register current provider
		$provider->register();

		foreach ($options as $key => $value) {
			$this[$key] = $value;
		}
		
		//Mark current provider as registered
		$this->markAsRegistered($provider);

		$provider->boot();
	}

	/**
	 * Mark the given provider as registered
	 * 
	 * @param Repository\Component\Support\ServiceProvider $provider
	 * 
	 * @return void
	 */
	public function markAsRegistered(&$provider)
	{
		$name = get_class($provider);

		$this->loadedProviders[$name] = $name;
		$this->serviceProviders[$name] = $provider;
	}

	/**
	 * Get the registered service provider instance if it exists.
	 *
	 * @param  \Repository\Component\Support\ServiceProvider|string  $provider
	 * 
	 * @return \Repository\Component\Support\ServiceProvider|null
	 */
	public function getRegistered($provider)
	{
		$name = is_string($provider) ? $provider : get_class($provider);

		if (in_array($name, $this->loadedProviders)) {
			return $this->serviceProviders[$name];
		}

		return false;
	}

	/**
	 * Resolve a service provider instance from the class name.
	 *
	 * @param  string  $provider
	 * 
	 * @return \Repository\Component\Support\ServiceProvider
	 */
	public function resolveProviderClass($provider)
	{
		return new $provider($this);
	}

	/**
	 * Throw an HttpException with the given data.
	 *
	 * @param  int     $code
	 * @param  string  $message
	 * @param  array   $headers
	 * 
	 * @return void
	 *
	 * @throws \Repository\Component\Exception\Http\HttpException
	 * @throws \Repository\Component\Exception\Http\NotFoundHttpException
	 */
	public function abort($code, $message = '', array $headers = array())
	{
		$this['response']->withStatus($code);

		$debug = !$this['debug.config']->isThrowableAlertEnable();

		if ($debug && !$this['request']->isAjax() && !$this['request']->isXml()) {
			$custom = $this->generateCustomMessageByCode($code);
			$message = !$message && $custom ? $custom : $message;

			//Here the example you can disable rte template engine compiler
			$this['view']->disableCompiler();
			//And set the desired extension you wish
			$this['view']->setExtension('.php');
			$this['view']->make('error' . DS . $code);

			$this['view']->with('responses', ['code' => $code, 'message' => $message]);
			
			exit;
		}

		if ($code == 404) {
			throw new NotFoundHttpException($message);
		}
		
		throw new HttpException($code, $message, null, $headers);
	}

	/**
	 * Generate custom message by the given code
	 * 
	 * @param int $code
	 * 
	 * @return bool false|string
	 */
	public function generateCustomMessageByCode(int $code)
	{
		$messages = $this['config']['debug']['messages'];

		if (isset($messages[$code])) {
			return $messages[$code];
		}

		return false;
	}

	private function booted()
	{
		foreach ($this->serviceProviders as &$provider) {
			$provider->booted();
		}
	}

	/**
	 * Ready for accept requests and responses and run the application
	 * 
	 * @return void
	 */
	public function handle()
	{
		$this->booted();
		$this['route']->dispatch();
	}

	/**
	 * Determine if the application running in console
	 * 
	 * @return boolean
	 */
	public function isRunningInConsole()
	{
		return $this['request']->isRunningInConsole();
	}

	/**
	 * Get the service provider repository instance.
	 * 
	 * @return \Repository\Component\Foundation\ProviderRepository
	 */
	public function getProviderRepository()
	{
		$manifest = $this['config']['application.manifest'];
		
		return new ProviderRepository($manifest);
	}

	/**
	 * Get the configuration loader instance.
	 * 
	 * @return \Repository\Component\Contracts\Config\LoaderInterface
	 */
	public function getConfigLoader()
	{
		return new FileLoader();
	}

	/**
     * Determine if the application configuration is cached.
     *
     * @return bool
     */
    public function configurationIsCached()
    {
        return is_file($this->getCachedConfigPath());
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath()
    {
        return $this->normalizeCachePath('APP_CONFIG_CACHE', 'cache/config.php');
    }

    /**
     * Determine if the application routes are cached.
     *
     * @return bool
     */
    public function routesAreCached()
    {
        return file_exists($this->getCachedRoutesPath());
    }

    /**
     * Get the path to the routes cache file.
     *
     * @return string
     */
    public function getCachedRoutesPath()
    {
        return $this->normalizeCachePath('APP_ROUTES_CACHE', 'cache/routes.php');
    }

    /**
     * Check whether in development mode or not.
     *
     * @return bool
     */
    public function isDevMode()
    {
        return Config::get('application.environment') === 'development';
    }

    public function flush()
    {
    	$this->loadedProviders = [];
    	$this->serviceProviders = [];
    	$this->deferredServices = [];
    	$this->loadedDeferredProviders = [];
    }
}