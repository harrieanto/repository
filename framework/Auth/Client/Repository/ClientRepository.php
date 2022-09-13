<?php
namespace Repository\Component\Auth\Client\Repository;

use Repository\Component\Auth\Client\ClientEntityInterface;

/**
 * The Minimal Jwt Client Repository Requirement Must be Implement.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface ClientRepository
{
	/**
	 * Add new client to the data source
	 * 
	 * @param \Repository\Component\Auth\Client\ClientEntityInterface $client
	 *  
	 * @return bool true When persisted, false otherwise
	 */
	public function add(ClientEntityInterface $client)

	/**
	 * Get client informations by the given id
	 * 
	 * @param int|string $id
	 *  
	 * @return null|bool|array
	 */	
	public function getById($id);
}