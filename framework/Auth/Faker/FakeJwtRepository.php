<?php
namespace Repository\Component\Auth\Faker;

use Repository\Component\Cache\Repository;
use Repository\Component\Auth\Jwt\Repository\JwtRepository;

/**
 * The Fake Concrete Impelementation of Jwt Repository Interface.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class FakeJwtRepository implements JwtRepository
{
	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
	}
	
	public function has($signedjwt)
	{
		$signedJwt = $this->repository->getBySignedJwt($signedJwt);
		
		if ($signedJwt !== null) {
			return true;
		}
		
		return false;
	}
	
	public function getBySignedJwt(string $signedJwt)
	{
		$signedJwts = $this->repository->get('jwt')['payload'];

		if (isset($signedJwts['token'])) {
			$signedJwts = array_flip($signedJwts);

			return $signedJwts;
		}
	}
}