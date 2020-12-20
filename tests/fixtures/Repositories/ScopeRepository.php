<?php declare(strict_types = 1);

namespace Tests\Fixtures\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

final class ScopeRepository implements ScopeRepositoryInterface
{

	/**
	 * @inheritDoc
	 */
	public function getScopeEntityByIdentifier($identifier)
	{
		// TODO: Implement getScopeEntityByIdentifier() method.
	}

	/**
	 * @inheritDoc
	 */
	public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
	{
		// TODO: Implement finalizeScopes() method.
	}

}
