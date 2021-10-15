<?php

namespace OCA\MessageVault\Service;

use OCA\MessageVault\Db\AddressMapper;

use OCP\IUserSession;

class AddressService {
	private $address_mapper;

	public function __construct(AddressMapper $address_mapper) {
		$this->address_mapper = $address_mapper;
	}

	public function findAll(IUserSession $user) {
		return $this->address_mapper->findAll($user->getUser(), ['id', 'address', 'name']);
	}
}
