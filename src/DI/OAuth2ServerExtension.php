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
use Nette\Utils\Validators;
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
			'encryptionKey' => Expect::string(),
			'privateKey' => Expect::array([
				'path' => Expect::string(),
				'passPhrase' => Expect::string(),
				'permissionCheck' => Expect::bool(TRUE),
			]),
			'publicKey' => Expect::array([
				'path' => Expect::string(),
				'passPhrase' => Expect::string(),//todo Public key has no passphrase
				'permissionCheck' => Expect::bool(TRUE),
			]),
			'grants' => Expect::array([
				self::GRANT_AUTH_CODE => Expect::bool(FALSE),
				self::GRANT_CLIENT_CREDENTIALS => Expect::bool(FALSE),
				self::GRANT_IMPLICIT => Expect::bool(FALSE),
				self::GRANT_PASSWORD => Expect::bool(FALSE),
				self::GRANT_REFRESH_TOKEN => Expect::bool(FALSE),
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		// Encryption key
		Validators::assertField($config, 'encryptionKey', 'string|\Nette\DI\Statement', $this->prefix('encryptionKey'));

		// Keys
		$privateKey = $this->loadKey('privateKey');
		$publicKey = $this->loadKey('publicKey');

		// Servers
		$authServer = $builder->addDefinition($this->prefix('authorizationServer'))
			->setFactory(AuthorizationServer::class, [
				'privateKey' => $privateKey,
				'encryptionKey' => $config['encryptionKey'],
			]);

		$builder->addDefinition($this->prefix('resourceServer'))
			->setFactory(ResourceServer::class, [
				'publicKey' => $publicKey,
			]);

		// Grants
		Validators::assertField($config, 'grants', 'array');
		foreach ($config['grants'] as $grant => $options) {

			Validators::assert($options, 'array|bool');

			if ($options === FALSE) {
				continue;
			}

			$grantDefinition = $builder->addDefinition($this->prefix('grant.' . $grant));

			switch ($grant) {
				case self::GRANT_AUTH_CODE:
					if (isset($options['codeExchangeProof']) && $options['codeExchangeProof'] === TRUE) {
						$grantDefinition->addSetup('enableCodeExchangeProof');
					}

					if (isset($options['authCodeTTL']) && $options['authCodeTTL'] !== FALSE) {
						$ttl = $options['authCodeTTL'];
					} else {
						$ttl = 'PT10M';
					}
					if (!$ttl instanceof Statement) {
						$ttl = new Statement(DateInterval::class, [new DateInterval($ttl)]);
					}

					$grantDefinition->setFactory(AuthCodeGrant::class, ['authCodeTTL' => $ttl]);
					break;

				case self::GRANT_CLIENT_CREDENTIALS:
					$grantDefinition->setFactory(ClientCredentialsGrant::class);
					break;

				case self::GRANT_IMPLICIT:
					if (isset($options['accessTokenTTL']) && $options['accessTokenTTL'] !== FALSE) {
						$ttl = $options['accessTokenTTL'];
					} else {
						$ttl = 'PT10M';
					}
					if (!$ttl instanceof Statement) {
						$ttl = new Statement(DateInterval::class, [new DateInterval($ttl)]);
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

			$ttl = $options['ttl'] ?? NULL;
			if (!$ttl instanceof Statement && $ttl !== NULL) {
				$ttl = new Statement(DateInterval::class, [new DateInterval($ttl)]);
			}
			$authServer->addSetup('enableGrantType', [$grantDefinition, $ttl]);
		}
	}

	protected function loadKey(string $key): Statement
	{
		$config = $this->config;
		$config = $config->$key;

		Validators::assert($config, 'array', $this->prefix($key));
		Validators::assertField($config, 'path', 'string', $this->prefix($key . '.path'));
		Validators::assertField($config, 'passPhrase', 'string|null', $this->prefix($key . '.passPhrase'));
		Validators::assertField($config, 'permissionCheck', 'bool', $this->prefix($key . '.permissionCheck'));

		return new Statement(CryptKey::class, [$config->path, $config->passPhrase, $config->permissionCheck]);
	}

}
