<?php declare(strict_types = 1);

namespace Contributte\OAuth2Server\Client;

class DefaultISecretValidator implements ISecretValidator
{

	public function validate(?string $expected, string $actual): bool
	{
		if ($expected === NULL) {
			return FALSE;
		}

		return hash_equals($expected, $actual);
	}

}
