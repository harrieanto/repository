<?php
namespace Repository\Component\Contracts\Http;

/**
 * Jsonable Interface.
 * 
 * @package	 \Repository\Component\Contracts\Http
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface Jsonable
{

    /**
     * Convert body message to json format
     *  
     * @return string
     */
    public function toJson();
}