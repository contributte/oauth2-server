<?php declare(strict_types=1);


namespace Tests\Fixtures\Repositories;


use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

final class AuthCodeRepository implements AuthCodeRepositoryInterface
{

	/**
	 * @inheritDoc
	 */
	public function getNewAuthCode()
	{
		// TODO: Implement getNewAuthCode() method.
	}

	/**
	 * @inheritDoc
	 */
	public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
	{
		// TODO: Implement persistNewAuthCode() method.
	}

	/**
	 * @inheritDoc
	 */
	public function revokeAuthCode($codeId)
	{
		// TODO: Implement revokeAuthCode() method.
	}

	/**
	 * @inheritDoc
	 */
	public function isAuthCodeRevoked($codeId)
	{
		// TODO: Implement isAuthCodeRevoked() method.
	}
}
