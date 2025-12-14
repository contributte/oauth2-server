<?php declare(strict_types = 1);

namespace Tests\Cases\DI;

use Contributte\OAuth2Server\DI\OAuth2ServerExtension;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\FileMock;

require_once __DIR__ . '/../../bootstrap.php';

// Test basic configuration without grants
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTmpDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('oauth2.server', new OAuth2ServerExtension());
		$compiler->loadConfig(FileMock::create('
			oauth2.server:
				encryptionKey: "fake"
				privateKey:
					path: "../../Fixtures/keys/private.key"
					passPhrase: "foo"
					permissionCheck: false
				publicKey:
					path: "../../Fixtures/keys/public.key"
					permissionCheck: false

			services:
				- Tests\Fixtures\Repositories\ClientRepository
				- Tests\Fixtures\Repositories\AccessTokenRepository
				- Tests\Fixtures\Repositories\ScopeRepository
				- Tests\Fixtures\Repositories\AuthCodeRepository
				- Tests\Fixtures\Repositories\RefreshTokenRepository
				- Tests\Fixtures\Repositories\UserRepository
		', 'neon'));
	}, [getmypid(), 1]);

	/** @var Container $container */
	$container = new $class();

	Assert::count(1, $container->findByType(AuthorizationServer::class));
	Assert::count(1, $container->findByType(ResourceServer::class));
});

// Test encryption key as Statement
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTmpDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('oauth2.server', new OAuth2ServerExtension());
		$compiler->loadConfig(FileMock::create('
			oauth2.server:
				encryptionKey: Defuse\Crypto\Key::loadFromAsciiSafeString("fake")
				privateKey:
					path: "../../Fixtures/keys/private.key"
					passPhrase: "foo"
					permissionCheck: false
				publicKey:
					path: "../../Fixtures/keys/public.key"
					permissionCheck: false

			services:
				- Tests\Fixtures\Repositories\ClientRepository
				- Tests\Fixtures\Repositories\AccessTokenRepository
				- Tests\Fixtures\Repositories\ScopeRepository
				- Tests\Fixtures\Repositories\AuthCodeRepository
				- Tests\Fixtures\Repositories\RefreshTokenRepository
				- Tests\Fixtures\Repositories\UserRepository
		', 'neon'));
	}, [getmypid(), 2]);

	/** @var Container $container */
	$container = new $class();

	Assert::count(1, $container->findByType(AuthorizationServer::class));
	Assert::count(1, $container->findByType(ResourceServer::class));
});

// Test grants with default TTL
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTmpDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('oauth2.server', new OAuth2ServerExtension());
		$compiler->loadConfig(FileMock::create('
			oauth2.server:
				encryptionKey: "fake"
				privateKey:
					path: "../../Fixtures/keys/private.key"
					passPhrase: "foo"
					permissionCheck: false
				publicKey:
					path: "../../Fixtures/keys/public.key"
					permissionCheck: false
				grants:
					clientCredentials: []
					password: []
					refreshToken: []

			services:
				- Tests\Fixtures\Repositories\ClientRepository
				- Tests\Fixtures\Repositories\AccessTokenRepository
				- Tests\Fixtures\Repositories\ScopeRepository
				- Tests\Fixtures\Repositories\AuthCodeRepository
				- Tests\Fixtures\Repositories\RefreshTokenRepository
				- Tests\Fixtures\Repositories\UserRepository
		', 'neon'));
	}, [getmypid(), 3]);

	/** @var Container $container */
	$container = new $class();

	Assert::count(1, $container->findByType(AuthorizationServer::class));
	Assert::count(1, $container->findByType(ClientCredentialsGrant::class));
	Assert::count(1, $container->findByType(PasswordGrant::class));
	Assert::count(1, $container->findByType(RefreshTokenGrant::class));
});

// Test grants with custom TTL (issue #14)
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTmpDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('oauth2.server', new OAuth2ServerExtension());
		$compiler->loadConfig(FileMock::create('
			oauth2.server:
				encryptionKey: "fake"
				privateKey:
					path: "../../Fixtures/keys/private.key"
					passPhrase: "foo"
					permissionCheck: false
				publicKey:
					path: "../../Fixtures/keys/public.key"
					permissionCheck: false
				grants:
					clientCredentials:
						ttl: PT30M
					password:
						ttl: PT1H
					refreshToken:
						ttl: P7D

			services:
				- Tests\Fixtures\Repositories\ClientRepository
				- Tests\Fixtures\Repositories\AccessTokenRepository
				- Tests\Fixtures\Repositories\ScopeRepository
				- Tests\Fixtures\Repositories\AuthCodeRepository
				- Tests\Fixtures\Repositories\RefreshTokenRepository
				- Tests\Fixtures\Repositories\UserRepository
		', 'neon'));
	}, [getmypid(), 4]);

	/** @var Container $container */
	$container = new $class();

	Assert::count(1, $container->findByType(AuthorizationServer::class));
	Assert::count(1, $container->findByType(ClientCredentialsGrant::class));
	Assert::count(1, $container->findByType(PasswordGrant::class));
	Assert::count(1, $container->findByType(RefreshTokenGrant::class));
});

// Test authCode grant with all options
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTmpDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('oauth2.server', new OAuth2ServerExtension());
		$compiler->loadConfig(FileMock::create('
			oauth2.server:
				encryptionKey: "fake"
				privateKey:
					path: "../../Fixtures/keys/private.key"
					passPhrase: "foo"
					permissionCheck: false
				publicKey:
					path: "../../Fixtures/keys/public.key"
					permissionCheck: false
				grants:
					authCode:
						ttl: PT1H
						authCodeTTL: PT5M
						codeExchangeProof: true

			services:
				- Tests\Fixtures\Repositories\ClientRepository
				- Tests\Fixtures\Repositories\AccessTokenRepository
				- Tests\Fixtures\Repositories\ScopeRepository
				- Tests\Fixtures\Repositories\AuthCodeRepository
				- Tests\Fixtures\Repositories\RefreshTokenRepository
				- Tests\Fixtures\Repositories\UserRepository
		', 'neon'));
	}, [getmypid(), 5]);

	/** @var Container $container */
	$container = new $class();

	Assert::count(1, $container->findByType(AuthorizationServer::class));
	Assert::count(1, $container->findByType(AuthCodeGrant::class));
});

// Test implicit grant with accessTokenTTL
Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTmpDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('oauth2.server', new OAuth2ServerExtension());
		$compiler->loadConfig(FileMock::create('
			oauth2.server:
				encryptionKey: "fake"
				privateKey:
					path: "../../Fixtures/keys/private.key"
					passPhrase: "foo"
					permissionCheck: false
				publicKey:
					path: "../../Fixtures/keys/public.key"
					permissionCheck: false
				grants:
					implicit:
						ttl: PT2H
						accessTokenTTL: PT15M

			services:
				- Tests\Fixtures\Repositories\ClientRepository
				- Tests\Fixtures\Repositories\AccessTokenRepository
				- Tests\Fixtures\Repositories\ScopeRepository
				- Tests\Fixtures\Repositories\AuthCodeRepository
				- Tests\Fixtures\Repositories\RefreshTokenRepository
				- Tests\Fixtures\Repositories\UserRepository
		', 'neon'));
	}, [getmypid(), 6]);

	/** @var Container $container */
	$container = new $class();

	Assert::count(1, $container->findByType(AuthorizationServer::class));
	Assert::count(1, $container->findByType(ImplicitGrant::class));
});
