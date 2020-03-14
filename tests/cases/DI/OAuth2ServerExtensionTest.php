<?php declare(strict_types = 1);

use Contributte\OAuth2Server\DI\OAuth2ServerExtension;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\ResourceServer;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\FileMock;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('oauth2Server', new OAuth2ServerExtension());
		$compiler->loadConfig(FileMock::create('
			oauth2Server:
				encryptionKey: \'Fc+FESy6/yfOlXMBW65BXoSZsfWJkP5jCV9w0fyFfw4=\'
				privateKey:
					path: \'../../fixtures/keys/private.key\'
					passPhrase: foo
					permissionCheck: true
				publicKey:
					path: \'../../fixtures/keys/public.key\'
					passPhrase:
					permissionCheck: true
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
