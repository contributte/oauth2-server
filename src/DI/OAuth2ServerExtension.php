<?php declare(strict_types = 1);

namespace Contributte\OAuth2Server\DI;

use Contributte\OAuth2Server\Exception\InvalidArgumentException;
use DateInterval;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
class OAuth2ServerExtension extends CompilerExtension
{

	private const GRANT_AUTH_CODE = 'authCode';
	private const GRANT_CLIENT_CREDENTIALS = 'clientCredentials';
	private const GRANT_IMPLICIT = 'implicit';
	private const GRANT_PASSWORD = 'password';
	private const GRANT_REFRESH_TOKEN = 'refreshToken';

	private const GRANTS = [
		self::GRANT_AUTH_CODE,
		self::GRANT_CLIENT_CREDENTIALS,
		self::GRANT_IMPLICIT,
		self::GRANT_PASSWORD,
		self::GRANT_REFRESH_TOKEN,
	];

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'encryptionKey' => Expect::anyOf(Expect::string(), Expect::type(Statement::class)),
			'privateKey' => Expect::array([
				'path' => Expect::string(),
				'passPhrase' => Expect::string(),
				'permissionCheck' => Expect::bool(true),
			]),
			'publicKey' => Expect::array([
				'path' => Expect::string(),
				'permissionCheck' => Expect::bool(true),
			]),
			'grants' => Expect::array([
				self::GRANT_AUTH_CODE => Expect::bool(false),
				self::GRANT_CLIENT_CREDENTIALS => Expect::bool(false),
				self::GRANT_IMPLICIT => Expect::bool(false),
				self::GRANT_PASSWORD => Expect::bool(false),
				self::GRANT_REFRESH_TOKEN => Expect::bool(false),
			]),
			'responseType' => Expect::type(Statement::class),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		// Keys
		$privateKey = $this->loadPrivateKey();
		$publicKey = $this->loadPublicKey();

		// Servers
		$authServer = $builder->addDefinition($this->prefix('authorizationServer'))
			->setFactory(AuthorizationServer::class, [
				'privateKey' => $privateKey,
				'encryptionKey' => $config->encryptionKey,
				'responseType' => $config->responseType,
			]);

		$builder->addDefinition($this->prefix('resourceServer'))
			->setFactory(ResourceServer::class, [
				'publicKey' => $publicKey,
			]);

		// Grants
		foreach ($config->grants as $grant => $options) {
			if ($options === false) {
				continue;
			}

			$grantDefinition = $builder->addDefinition($this->prefix('grant.' . $grant));

			switch ($grant) {
				case self::GRANT_AUTH_CODE:
					if (isset($options->codeExchangeProof) && $options->codeExchangeProof === true) {
						$grantDefinition->addSetup('enableCodeExchangeProof');
					}

					$ttl = isset($options->authCodeTTL) && $options->authCodeTTL !== false ? $options->authCodeTTL : 'PT10M';

					if (!$ttl instanceof Statement) {
						$ttl = new Statement(DateInterval::class, [$ttl]);
					}

					$grantDefinition->setFactory(AuthCodeGrant::class, ['authCodeTTL' => $ttl]);
					break;
				case self::GRANT_CLIENT_CREDENTIALS:
					$grantDefinition->setFactory(ClientCredentialsGrant::class);
					break;
				case self::GRANT_IMPLICIT:
					$ttl = isset($options->accessTokenTTL) && $options->accessTokenTTL !== false ? $options->accessTokenTTL : 'PT10M';

					if (!$ttl instanceof Statement) {
						$ttl = new Statement(DateInterval::class, [$ttl]);
					}

					$grantDefinition->setFactory(ImplicitGrant::class, ['accessTokenTTL' => $ttl]);
					break;
				case self::GRANT_PASSWORD:
					$grantDefinition->setFactory(PasswordGrant::class);
					break;
				case self::GRANT_REFRESH_TOKEN:
					$grantDefinition->setFactory(RefreshTokenGrant::class);
					break;
				default:
					throw new InvalidArgumentException(sprintf(
						'Invalid or unsupported grant type "%s". Supported are %s.',
						$grant,
						implode(', ', self::GRANTS)
					));
			}

			$ttl = $options->ttl ?? null;
			if (!$ttl instanceof Statement && $ttl !== null) {
				$ttl = new Statement(DateInterval::class, [$ttl]);
			}

			$authServer->addSetup('enableGrantType', [$grantDefinition, $ttl]);
		}
	}

	protected function loadPublicKey(): Statement
	{
		$config = $this->config;
		$config = $config->publicKey;

		return new Statement(CryptKey::class, [$config['path'], null, $config['permissionCheck']]);
	}

	protected function loadPrivateKey(): Statement
	{
		$config = $this->config;
		$config = $config->privateKey;

		return new Statement(CryptKey::class, [$config['path'], $config['passPhrase'], $config['permissionCheck']]);
	}

}
