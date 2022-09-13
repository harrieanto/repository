<?php
namespace Repository\Component\Bootstrappers;

use Repository\Component\Config\Config;
use Repository\Component\Config\Repository;

/**
 * Application File Configurations Bootstrapper.
 *
 * @package	  \Repository\Component\Bootstrap
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class FileRepositoryBootstrap extends Bootstrap
{
	/**
	 * @{inheritdoc}
	 * See \Repository\Component\Contracts\Bootstrap\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
		$items = [];

        if (is_file($cached = $this->app->getCachedConfigPath())) {
            $items = require $cached;

            $loadedFromCache = true;
        }

        if (isset($loadedFromCache)) {
			foreach ((array) $items as $key => $values) {
				Config::set($key, $values);
			}
        }


        if (!isset($loadedFromCache)) {
			foreach ((array) glob(CONFIG_ROOT_PATH . '/*') as $path) {
				$parts = explode('/', $path);
				$keys = explode('.', end($parts));

				if (file_exists($path)) {
					if ($keys[1] === 'php') {
						Config::set($keys[0], require $path);
					}
				}
			}

			$this->cacheConfigs();
        }

		$this->registerFileRepository();
	}

	/**
	 * Cache available configurations
	 * 
	 * @return void
	 */
	public function cacheConfigs()
	{
		$path = $this->app->getCachedConfigPath();

		$data = Config::all();
		unset($data['env']);

        $data = "<?php\n\nreturn " .var_export($data, true) .";\n";

		$handle = fopen($path, 'w+');

		fwrite($handle, $data);
		fclose($handle);
	}

	/**
	 * Register file configuration repository
	 * @return void
	 */	
	private function registerFileRepository()
	{
		$repository = new Repository($this->app->getConfigLoader());
		$this->app->instance('config', $repository);
	}
}