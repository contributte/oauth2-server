<?php declare(strict_types = 1);

namespace Contributte\OAuth2Server\Http;

use Nette\Application\Response as AppResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Psr\Http\Message\ResponseInterface;

class Oauth2Response implements AppResponse
{

	public function __construct(
		private ResponseInterface $psr7,
	)
	{
	}

	public function send(IRequest $httpRequest, IResponse $httpResponse): void
	{
		$httpResponse->setCode($this->psr7->getStatusCode());

		foreach ($this->psr7->getHeaders() as $name => $values) {
			foreach ($values as $value) {
				$httpResponse->addHeader($name, $value);
			}
		}

		$stream = $this->psr7->getBody();
		$stream->rewind();

		while (!$stream->eof()) {
			echo $stream->read(8192);
		}
	}

}
