<?php declare(strict_types = 1);

namespace Tests\Fixtures\Repositories;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

final class AccessTokenRepository implements AccessTokenRepositoryInterface
{

	/**
	 * @inheritDoc
	 */
	public function getNewToken(
		ClientEntityInterface $clientEntity,
		array $scopes,
		string|null $userIdentifier = null
	): AccessTokenEntityInterface
	{
		// TODO: Implement getNewToken() method.
	}

	/**
	 * @inheritDoc
	 */
	public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
	{
		// TODO: Implement persistNewAccessToken() method.
	}

	/**
	 * @inheritDoc
	 */
	public function revokeAccessToken(string $tokenId): void
	{
		// TODO: Implement revokeAccessToken() method.
	}

	/**
	 * @inheritDoc
	 */
	public function isAccessTokenRevoked(string $tokenId): bool
	{
		// TODO: Implement isAccessTokenRevoked() method.
		return true;
	}

}
