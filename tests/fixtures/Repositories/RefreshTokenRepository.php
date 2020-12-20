<?php declare(strict_types = 1);

namespace Tests\Fixtures\Repositories;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

final class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{

	/**
	 * @inheritDoc
	 */
	public function getNewRefreshToken()
	{
		// TODO: Implement getNewRefreshToken() method.
	}

	/**
	 * @inheritDoc
	 */
	public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
	{
		// TODO: Implement persistNewRefreshToken() method.
	}

	/**
	 * @inheritDoc
	 */
	public function revokeRefreshToken($tokenId)
	{
		// TODO: Implement revokeRefreshToken() method.
	}

	/**
	 * @inheritDoc
	 */
	public function isRefreshTokenRevoked($tokenId)
	{
		// TODO: Implement isRefreshTokenRevoked() method.
	}

}
