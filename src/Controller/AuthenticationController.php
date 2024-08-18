<?php

declare(strict_types=1);

namespace App\Controller;

use OpenApi\Attributes as OA;

/**
 * Dummy class to document API authentication route.
 */
#[
    OA\Post(
        description: 'Authenticates.',
        path: '/authentication',
        summary: 'Authenticates.',
        /** @infection-ignore-all */
        tags: ['Authentication']
    ),
    OA\RequestBody(
        content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationCredentials'),
        description: 'The authentication credentials.',
        /** @infection-ignore-all */
        required: true
    ),
    OA\Response(
        content: new OA\JsonContent(ref: '#/components/schemas/AuthenticationToken'),
        description: 'When the authentication succeeds.',
        response: '200'
    ),
    OA\Response(
        description: 'When the credentials are not valid.',
        response: '401'
    ),
    OA\Response(
        description: "When the method is not allowed.",
        headers: [
            new OA\Header(
                description: 'POST',
                header: 'Allow',
                schema: new OA\Schema(type: 'string')
            )
        ],
        response: '405'
    ),
    OA\Response(
        ref: '#/components/responses/500',
        response: '500'
    )
]
final class AuthenticationController
{
}
