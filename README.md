![](https://heatbadger.now.sh/github/readme/contributte/oauth2-server/)

<p align=center>
  <a href="https://github.com/contributte/oauth2-server/actions"><img src="https://badgen.net/github/checks/contributte/oauth2-server/master?cache=300"></a>
  <a href="https://coveralls.io/r/contributte/oauth2-server"><img src="https://badgen.net/coveralls/c/github/contributte/oauth2-server?cache=300"></a>
  <a href="https://packagist.org/packages/contributte/oauth2-server"><img src="https://badgen.net/packagist/dm/contributte/oauth2-server"></a>
  <a href="https://packagist.org/packages/contributte/oauth2-server"><img src="https://badgen.net/packagist/v/contributte/oauth2-server"></a>
</p>
<p align=center>
  <a href="https://packagist.org/packages/contributte/oauth2-server"><img src="https://badgen.net/packagist/php/contributte/oauth2-server"></a>
  <a href="https://github.com/contributte/oauth2-server"><img src="https://badgen.net/github/license/contributte/oauth2-server"></a>
  <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
  <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
  <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

An integration of League\OAuth2Server into Nette framework. See the official [League OAuth2 Server documentation](https://oauth2.thephpleague.com/) for details about OAuth server concepts.

## Versions

| State       | Version | Branch   | Nette | PHP     |
|-------------|---------|----------|-------|---------|
| dev         | `^0.6`  | `master` | 3.2+  | `>=8.2` |
| stable      | `^0.5`  | `master` | 3.2+  | `>=8.2` |

## Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Example](#example)

## Installation

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
its [documentation](https://oauth2.thephpleague.com/). This package provides a tiny wrapper and integration into Nette
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

## Development

See [how to contribute](https://contributte.org) to this package. This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
    <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners) **contributte** development team.
Also thank you for using this package.
