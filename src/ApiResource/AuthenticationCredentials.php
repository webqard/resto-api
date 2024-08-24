<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;

/**
 * The authentication credentials.
 */
// @codeCoverageIgnoreStart
#[
    OA\Schema(
        type: "object",
        schema: "AuthenticationCredentials",
        description: "The authentication credentials.",
        properties: [
            new OA\Property(
                property: 'login',
                type: 'string',
                description: 'the login.',
                example : "login"
            ),
            new OA\Property(
                property: 'password',
                type: 'string',
                description: 'the password.',
                example : "password"
            )
        ]
    )
]
// @codeCoverageIgnoreEnd
class AuthenticationCredentials
{
}
