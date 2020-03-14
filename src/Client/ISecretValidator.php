<?php declare(strict_types = 1);

namespace Contributte\OAuth2Server\Client;

interface ISecretValidator
{

	public function validate(?string $expected, string $actual): bool;

}
