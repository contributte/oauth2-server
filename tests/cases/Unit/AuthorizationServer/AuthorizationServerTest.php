<?php declare(strict_types=1);

use Contributte\OAuth2Server\DI\OAuth2ServerExtension;
use Contributte\Psr7\Psr7Response;
use Contributte\Psr7\Psr7ServerRequest;
use Contributte\Psr7\Psr7Stream;
use GuzzleHttp\Psr7\Uri;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\FileMock;
use Tests\Fixtures\Repositories\RefreshTokenRepository;

require_once __DIR__ . '/../../../bootstrap.php';

test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('oauth2.server', new OAuth2ServerExtension());
		$currentDir = __DIR__;
		$compiler->loadConfig(FileMock::create(sprintf('
			oauth2.server:
				encryptionKey: "Fc+FESy6/yfOlXMBW65BXoSZsfWJkP5jCV9w0fyFfw4="
				privateKey:
					path: "%s/../../../fixtures/keys/private.key"
					passPhrase: foo
					permissionCheck: false
				publicKey:
					path: "%s/../../../fixtures/keys/public.key"
					permissionCheck: false
				grants:
					refreshToken: true

			services:
				- Tests\Fixtures\Repositories\ClientRepository
				- Tests\Fixtures\Repositories\AccessTokenRepository
				- Tests\Fixtures\Repositories\ScopeRepository
				- Tests\Fixtures\Repositories\AuthCodeRepository
				- Tests\Fixtures\Repositories\RefreshTokenRepository
				- Tests\Fixtures\Repositories\UserRepository
		', $currentDir, $currentDir), 'neon'));
	}, [getmypid(), 1]);

	/** @var Container $container */
	$container = new $class();

	/** @var AuthorizationServer $authorizationServer */
	$authorizationServer = $container->getByType(AuthorizationServer::class);

	/** @var RefreshTokenRepository $refreshTokenRepository */
	$refreshTokenRepository = $container->getByType(RefreshTokenRepositoryInterface::class);

	$grant = new RefreshTokenGrant($refreshTokenRepository);
	$grant->setRefreshTokenTTL(new DateInterval('P1M'));

	$authorizationServer->enableGrantType($grant, new DateInterval('PT1H'));

	$request = new Psr7ServerRequest(
		'GET',
		new Uri('http://example.com')
	);

	$response = new Psr7Response();

	try {
		$reply = $authorizationServer->respondToAccessTokenRequest($request, $response);
	} catch (OAuthServerException $exception) {
		$reply = $exception->generateHttpResponse($response);
	} catch (Exception $exception) {
		$body = new Psr7Stream('php://temp');
		$body->write($exception->getMessage());
		$reply = $response->withStatus(500)->withBody($body);
	}

	Assert::equal(200, $reply->getStatusCode());
});
