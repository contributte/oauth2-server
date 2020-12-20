<?php declare(strict_types = 1);

namespace Tests\Fixtures\Repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

final class ClientRepository implements ClientRepositoryInterface
{

	/**
	 * @inheritDoc
	 */
	public function getClientEntity($clientIdentifier)
	{
		// TODO: Implement getClientEntity() method.
	}

	/**
	 * @inheritDoc
	 */
	public function validateClient($clientIdentifier, $clientSecret, $grantType)
	{
		// TODO: Implement validateClient() method.
	}

}
