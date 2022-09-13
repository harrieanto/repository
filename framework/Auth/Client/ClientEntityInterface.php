<?php
namespace Repository\Component\Auth\Client;

/**
 * The Minimal Jwt Client Entity Requirement Must be Implement.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface ClientEntityInterface
{
	public function getId();
	
	public function getName();
	
	public function getIp();

	public function setId($id);
	
	public function setIp($ip);

	public function setName(string $clientName);
}