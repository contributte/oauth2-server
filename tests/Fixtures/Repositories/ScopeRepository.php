<?php declare(strict_types = 1);

namespace Tests\Fixtures\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

final class ScopeRepository implements ScopeRepositoryInterface
{

	/**
	 * @inheritDoc
	 */
	public function getScopeEntityByIdentifier(string $identifier): ?ScopeEntityInterface
	{
		// TODO: Implement getScopeEntityByIdentifier() method.
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function finalizeScopes(
		array $scopes,
		string $grantType,
		ClientEntityInterface $clientEntity,
		string|null $userIdentifier = null,
		?string $authCodeId = null
	): array
	{
		// TODO: Implement finalizeScopes() method.
		return [];
	}

}
