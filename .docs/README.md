# Contributte OAuth2 Server

## Content

- [Setup](#setup)
- [Configuration](#configuration)
- [Example](#example)

## Prologue

`Contributte/OAuth2Server` brings `League/OAuth2Server` to your Nette applications.

Please take a look at official documentation: [https://oauth2.thephpleague.com/](https://oauth2.thephpleague.com/)

## Setup

```bash
composer require contributte/oauth2-server
```

You also need to generate public and private key and an encryption key, for more information how to do it check
out `League/OAuth2Server` documentation: https://oauth2.thephpleague.com/installation/.

```neon
extensions:
	oauth2.server: Contributte\OAuth2Server\DI\OAuth2ServerExtension
```

## Configuration

Do not forget to change the permissions on your public and private key (`chmod 0600 public.key private.key`)
Or you can turn off the permission check in configuration (`permissionCheck`) - **not recommended**.

```neon
oauth2.server:
	encryptionKey: "encryption key"
	privateKey:
		path: "/path/to/private.key"
		passPhrase: "foo"
		permissionCheck: true
	publicKey:
		path: "/path/to/public.key"
		permissionCheck: true
	grants:
		authCode:
			ttl: PT1H
		clientCredentials:
			ttl: PT1H
		implicit:
			ttl: PT1H
		password:
			ttl: PT1H
		refreshToken:
			ttl: P7D
```

### Grant Configuration

Each grant type accepts an object with options. Use empty object `[]` to enable with defaults, or `false` to disable.

**Common option:**
- `ttl` - Access token lifetime (ISO 8601 duration)

**authCode grant:**
- `authCodeTTL` - Authorization code lifetime (default: `PT10M`)
- `codeExchangeProof` - Enable PKCE (default: `false`)

```neon
grants:
	authCode:
		ttl: PT1H
		authCodeTTL: PT5M
		codeExchangeProof: true
```

**implicit grant:**
- `accessTokenTTL` - Access token TTL for grant construction (default: `PT10M`)

```neon
grants:
	implicit:
		ttl: PT2H
		accessTokenTTL: PT15M
```

**TTL format** uses [ISO 8601 duration](https://en.wikipedia.org/wiki/ISO_8601#Durations): `PT10M` (10 min), `PT1H` (1 hour), `P1D` (1 day), `P7D` (7 days)

For encryption key, you can use `Defuse\Crypt\Key::loadFromAsciiSafeString($string)` or key in a string form.

```neon
oauth2.server:
	encryptionKey: Defuse\Crypto\Key::loadFromAsciiSafeString('keyInStringForm')
	# ...
```

Do not forget to register repositories as a services!

For more information about The PHP League's OAuth2 server, check out
it's [documentation](https://oauth2.thephpleague.com/). This package provides tiny wrappaper and integration into Nette
framework.

## Example

```php
<?php declare(strict_types = 1);

namespace App\Presenters;

use Contributte\OAuth2Server\Http\Oauth2Response;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use GuzzleHttp\Psr7\Utils;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;
use Nette\Http\IRequest;
use Throwable;

class OAuth2Presenter extends Presenter
{

	/** @var AuthorizationServer @inject */
	public $authorizationServer;

	public function actionEndpoint(): void
	{
		/** @var IRequest $request */
		$request = $this->getHttpRequest();
		$psr7Request = Psr7ServerRequestFactory::fromNette($request);
		/** @var IResponse $response */
		$response = $this->gethttpResponse();
		$psr7Response = Psr7ResponseFactory::fromNette($response);

		try {
			$reply = $this->authorizationServer->respondToAccessTokenRequest($psr7Request, $psr7Response);
		} catch (OAuthServerException $exception) {
			$reply = $exception->generateHttpResponse($psr7Response);
		} catch (Throwable $exception) {
			$body = Utils::streamFor('php://temp');
			$body->write($exception->getMessage());
			$reply = $psr7Response->withStatus(500)->withBody($body);
		}

		$this->sendResponse(new Oauth2Response($reply));
	}

}
```
