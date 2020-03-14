<?php declare(strict_types = 1);

namespace Contributte\OAuth2Server\Scope;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class DefaultScopeFinalizer implements IScopeFinalizer
{

	/**
	 * @param ScopeEntityInterface[] $scopes
	 * @return ScopeEntityInterface[]
	 */
	public function finalize(array $scopes, string $grantType, ClientEntityInterface $clientEntity, ?string $userIdentifier = null): array
	{
		return $scopes;
	}

}
