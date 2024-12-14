<?php declare(strict_types = 1);

namespace Tests\Fixtures\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

final class ClientRepository implements ClientRepositoryInterface
{

	/**
	 * @inheritDoc
	 */
	public function getClientEntity(string $clientIdentifier): ?ClientEntityInterface
	{
		// TODO: Implement getClientEntity() method.
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function validateClient(string $clientIdentifier, ?string $clientSecret, ?string $grantType): bool
	{
		// TODO: Implement validateClient() method.
		return false;
	}

}
