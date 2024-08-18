<?php

declare(strict_types=1);

namespace App\ApiResource;

use OpenApi\Attributes as OA;

/**
 * The authentication credentials.
 */
#[
    OA\Schema(
        type: "object",
        schema: "AuthenticationCredentials"
    )
]
class AuthenticationCredentials
{
    // Properties :

    /**
     * @var string the login.
     */
    #[OA\Property(example : "login")]
    private string $login;

    /**
     * @var string the password.
     */
    #[OA\Property(example : "password")]
    private string $password;
}
