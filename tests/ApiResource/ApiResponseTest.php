<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\ApiResponse;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API response.
 */
#[
    PA\CoversClass(ApiResponse::class),
    PA\Group('apiResource'),
    PA\Group('apiResource_apiResponse')
]
final class ApiResponseTest extends TestCase
{
    // Methods :

    /**
     * Tests that the message can be serialised.
     * @return void
     */
    public function testCanSerialiseMessage(): void
    {
        $apiResponse = new ApiResponse('test-message');

        $unserialisedResponse = json_decode(json_encode($apiResponse));

        self::assertSame('test-message', $unserialisedResponse->message, 'The message has been altered.');
    }
}
