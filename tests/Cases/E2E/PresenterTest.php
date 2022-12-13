<?php declare(strict_types = 1);

namespace Tests\Cases\E2E;

use Contributte\OAuth2Server\Http\Oauth2Response;
use Contributte\Psr7\Psr7Response;
use Nette\Application\IPresenter;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\Http\RequestFactory;
use Nette\Http\Response;
use Ninjify\Nunjuck\Toolkit;
use Psr\Http\Message\ResponseInterface;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$presenter = new class implements IPresenter {

		/** @var ResponseInterface */
		public $psr7;

		public function run(Request $request): IResponse
		{
			return new Oauth2Response($this->psr7);
		}

	};

	$psr7 = Psr7Response::fromGlobals()
		->withStatus(200);
	$psr7->getBody()->write('test');

	$presenter->psr7 = $psr7;
	$response = $presenter->run(new Request('test'));

	ob_start();
	$response->send((new RequestFactory())->fromGlobals(), new Response());
	$result = ob_get_contents();
	ob_end_clean();

	Assert::equal(200, http_response_code());
	Assert::equal('test', $result);
});
