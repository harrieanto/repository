<?php
namespace Repository\Component\Auth\Faker;

use Repository\Component\Auth\Client\ClientEntityInterface;

/**
 * The Fake Concrete Impelementation of Client Interface.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class FakeClientEntity implements ClientEntityInterface
{
	private $id;
	private $ip;
	private $clientName;

	public function __construct($items)
	{
		if (isset(($items['id']))) {
			$this->id = $items['id'];
		}
		
		if (isset($items['ip'])) {
			$this->ip = $items['ip'];
		}

		if (isset($items['client_name'])) {
			$this->clientName = $items['client_name'];
		}
	}

	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function setIp($ip)
	{
		$this->ip = $ip;
	}

	public function setName(string $clientName)
	{
		$this->clientName;
	}

	public function getId()
	{
		return $this->id;
	}
	
	public function getIp()
	{
		return $this->ip;
	}

	public function getName()
	{
		return $this->clientName;
	}
}