<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;

/**
 * The authentication token.
 */
#[
    OA\Schema(
        type: "object",
        schema: "AuthenticationToken",
        description: "The authentication token.",
        properties: [
            new OA\Property(
                property: 'token',
                type: 'string',
                description: 'the token.',
                example : "icDY0MjgslsiUDY0MjgsX0dF"
            )
        ]
    )
]
class AuthenticationToken
{
}
