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

You also need to generate public and private key and an encryption key, for more information how to do it check out `League/OAuth2Server` documentation: https://oauth2.thephpleague.com/installation/.

```yaml
extensions:
    oauth2.server: Contributte\OAuth2Server\DI\OAuth2ServerExtension
```

## Configuration

Do not forget to change the permissions on your public and private key (`chmod 0600 public.key private.key`)
Or you can turn off the permission check in configuration (`permissionCheck`) - **not recommended**.

```yaml
oauth2.server:
  encryptionKey: "encryption key"
  privateKey:
    path: "/path/to/private.key"
    passPhrase: "foo"
    permissionCheck: true
  publicKey:
    path: "/path/to/public.key"
    passPhrase:
    permissionCheck: true
  grants:
    authCode: true
    clientCredentials: true
    implicit: true
    password: true
    refreshToken: true
```

For encryption key, you can use `Defuse\Crypt\Key::loadFromAsciiSafeString($string)` or key in a string form.
```yaml
oauth2.server:
  encryptionKey: Defuse\Crypto\Key::loadFromAsciiSafeString('keyInStringForm')
  # ...
```

Do not forget to register repositories as a services!

For more information about The PHP League's OAuth2 server, check out it's [documentation](https://oauth2.thephpleague.com/). This package provides tiny wrappaper and integration into Nette framework.


## Example

```php
<?php declare(strict_types = 1);

namespace App\Presenters;

use Contributte\Psr7\Psr7Response;
use Contributte\Psr7\Psr7ResponseFactory;
use Contributte\Psr7\Psr7ServerRequestFactory;
use Contributte\Psr7\Psr7Stream;
use League\OAuth2\Server\AuthorizationServer;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;
use Nette\Http\IRequest;

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
        } catch (Exception $exception) {
            $body = new Psr7Stream('php://temp');
            $body->write($exception->getMessage());
            $reply = $psr7Response->withStatus(500)->withBody($body);
        }

        $sendResponse = Psr7Response::of($reply);
        $this->sendResponse($sendResponse->getApplicationResponse());
    }

}
```
