<?php
namespace Repository\Component\Contracts\Loader;

/**
 * Configuration Loader Interface.
 * 
 * @package	 \Repository\Component\Contracts\Loader
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface LoaderInterface
{
    /**
     * Load the given configuration group.
     *
     * @param  string  $group
     * 
     * @return array
     */
    public function load($group);
}