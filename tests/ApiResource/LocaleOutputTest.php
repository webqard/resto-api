<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\ApiResponse;
use PHPUnit\Framework\TestCase;

/**
 * Tests "\App\ApiResource\ApiResponse".
 *
 * @coversDefaultClass \App\ApiResource\ApiResponse
 * @covers ::__construct
 * @covers ::jsonSerialize
 * @group ApiResource
 * @group ApiResource_ApiResponse
 */
final class ApiResponseTest extends TestCase
{
    // Méthodes :

    /**
     * Tests that the message can be serialised.
     * @return void
     *
     */
    public function testCanSerialiseMessage(): void
    {
        $apiResponse = new ApiResponse('test-message');

        $unserialisedResponse = json_decode(json_encode($apiResponse));

        self::assertObjectHasAttribute('message', $unserialisedResponse, 'The response does not have a message attribute.');
        self::assertSame('test-message', $unserialisedResponse->message, 'The message has been altered.');
    }
}
