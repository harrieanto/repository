<?php
namespace Repository\Component\Auth\Faker;

use Repository\Component\Auth\User\Repository\UserRepository;
use Repository\Component\Cache\Repository as RepositoryManager;

/**
 * The Fake Concrete Impelementation of User Repository Interface.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class FakeUserRepository implements UserRepository
{
	private $manager;

	public function __construct(RepositoryManager $manager)
	{
		$this->manager = $manager;
	}
	
	public function getByUsername($username)
	{
		$attributes = $this->manager->get('user')['payload'];

		if (!isset($attributes[$username])) {
			return false;
		}

		$user = new FakeUserEntity($attributes);

		return $user;
	}
	
	public function getById($id)
	{
		$attributes = $this->manager->get('user')['payload'];

		if (!isset($attributes[$id])) {
			return false;
		}

		$user = new FakeUserEntity($attributes[$id]);

		return $user;
	}
}