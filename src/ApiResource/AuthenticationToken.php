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
        schema: "AuthenticationToken"
    )
]
class AuthenticationToken
{
    // Properties :

    /**
     * @var string the token.
     */
    #[OA\Property(example : "icDY0MjgslsiUDY0MjgsX0dF")]
    private string $token;
}
