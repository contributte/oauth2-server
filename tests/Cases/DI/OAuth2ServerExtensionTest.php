<?php declare(strict_types = 1);

namespace Tests\Cases\DI;

use Contributte\OAuth2Server\DI\OAuth2ServerExtension;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\ResourceServer;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\FileMock;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTmpDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('oauth2.server', new OAuth2ServerExtension());
		$compiler->loadConfig(FileMock::create('
			oauth2.server:
				encryptionKey: "fake"
				privateKey:
					path: "../../fixtures/keys/private.key"
					passPhrase: "foo"
					permissionCheck: false
				publicKey:
					path: "../../fixtures/keys/public.key"
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

Toolkit::test(function (): void {
	$loader = new ContainerLoader(Environment::getTmpDir(), true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('oauth2.server', new OAuth2ServerExtension());
		$compiler->loadConfig(FileMock::create('
			oauth2.server:
				encryptionKey: Defuse\Crypto\Key::loadFromAsciiSafeString("fake")
				privateKey:
					path: "../../fixtures/keys/private.key"
					passPhrase: "foo"
					permissionCheck: false
				publicKey:
					path: "../../fixtures/keys/public.key"
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
