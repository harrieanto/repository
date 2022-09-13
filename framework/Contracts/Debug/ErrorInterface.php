<?php
namespace Repository\Component\Contracts\Debug;

/**
 * Debug Error Interface.
 * 
 * @package	 \Repository\Component\Contracts\Debug
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface ErrorInterface
{
    /**
     * Handles an error
     *
     * @param int $level The level of the error
     * @param string $message The error message
     * @param string $file The file the error occurred in
     * @param int $line The line number the error occurred at
     * @param array $context The symbol table
     * 
     * @throws ErrorException Thrown because the error is converted to an exception
     */
    public function handle(int $level, string $message, string $file = '', int $line = 0, array $context = []);

	/**
	 * Handle PHP shutdown function
	 */
    public function shutdown();
	
	/**
	 * Register error handler handler with PHP
	 */
	public function register();
}