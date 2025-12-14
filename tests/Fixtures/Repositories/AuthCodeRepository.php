<?php declare(strict_types = 1);

namespace Tests\Fixtures\Repositories;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

final class AuthCodeRepository implements AuthCodeRepositoryInterface
{

	/**
	 * @inheritDoc
	 */
	public function getNewAuthCode(): AuthCodeEntityInterface
	{
		// TODO: Implement getNewAuthCode() method.
	}

	/**
	 * @inheritDoc
	 */
	public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
	{
		// TODO: Implement persistNewAuthCode() method.
	}

	/**
	 * @inheritDoc
	 */
	public function revokeAuthCode(string $codeId): void
	{
		// TODO: Implement revokeAuthCode() method.
	}

	/**
	 * @inheritDoc
	 */
	public function isAuthCodeRevoked(string $codeId): bool
	{
		// TODO: Implement isAuthCodeRevoked() method.
		return true;
	}

}
