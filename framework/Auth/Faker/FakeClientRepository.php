<?php
namespace Repository\Component\Auth\Faker;

use Repository\Component\Cache\Repository;

/**
 * The Fake Concrete Impelementation of Client Repository Interface.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class FakeClientRepository
{
	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
	}
	
	public function getByUserId($id)
	{
		$clients = $this->repository->get('client')['payload'];
		$clients = $clients[$id];
		
		$client = new FakeClientEntity($clients);
		
		return $client;
	}
}