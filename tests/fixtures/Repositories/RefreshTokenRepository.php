<?php declare(strict_types = 1);

namespace Tests\Fixtures\Repositories;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

final class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{

	/**
	 * @inheritDoc
	 */
	public function getNewRefreshToken(): ?RefreshTokenEntityInterface
	{
		// TODO: Implement getNewRefreshToken() method.
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
	{
		// TODO: Implement persistNewRefreshToken() method.
	}

	/**
	 * @inheritDoc
	 */
	public function revokeRefreshToken(string $tokenId): void
	{
		// TODO: Implement revokeRefreshToken() method.
	}

	/**
	 * @inheritDoc
	 */
	public function isRefreshTokenRevoked(string $tokenId): bool
	{
		// TODO: Implement isRefreshTokenRevoked() method.
		return true;
	}

}
