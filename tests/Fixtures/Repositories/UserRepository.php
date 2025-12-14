<?php declare(strict_types = 1);

namespace Tests\Fixtures\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

final class UserRepository implements UserRepositoryInterface
{

	/**
	 * @inheritDoc
	 */
	public function getUserEntityByUserCredentials(
		string $username,
		string $password,
		string $grantType,
		ClientEntityInterface $clientEntity
	): ?UserEntityInterface
	{
		// TODO: Implement getUserEntityByUserCredentials() method.
		return null;
	}

}
