<?php
namespace Repository\Component\Auth;

use Repository\Component\Contracts\Auth\AuthInterface;
use Repository\Component\Contracts\Hashing\HashInterface;
use Repository\Component\Contracts\Auth\CredentialInterface;
use Repository\Component\Auth\User\Repository\UserRepository;

/**
 * Basic Username/Password Authenticator.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class UsernamePasswordAuth implements AuthInterface
{
	/**
	 * @var \Repository\Component\Auth\User\Repository\UserReposiotry $userRepository
	 */
	private $userRepository;

	/**
	 * @var \Repository\Component\Contracts\Hashing\HashInterface $hash
	 */
	private $hash;

	/**
	 * @param \Repository\Component\Auth\User\Repository\UserReposiotry $userRepository
	 * @param \Repository\Component\Contracts\Hashing\HashInterface $hash
	 */
	public function __construct(UserRepository $userRepository, HashInterface $hash)
	{
		$this->userRepository = $userRepository;
		$this->hash = $hash;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Auth\AuthInterface::authenticate()
	 */	
	public function authenticate(CredentialInterface $credential, &$error)
	{
		$username = $credential->getValue('username');
		$password = $credential->getValue('password');
		
		if ($username === null || $password === null) {
			$error = AuthErrorTypes::CREDENTIAL_IS_MISSING;
			return false;
		}

		$user = $this->userRepository->getByUsername($username);

		if (!$user || $user === null || empty($user)) {
			$error = AuthErrorTypes::USERNAME_NOT_FOUND;
			return false;
		}
		
		if (is_array($user)) {
			$user = $user[0];
		}
		
		if (!$this->hash->isValid($password, $user->getHashedPassword())) {
			$error = AuthErrorTypes::INVALID_PASSWORD;
			return false;
		}

		return true;
	}
}