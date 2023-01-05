<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\Violation;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API violation.
 *
 * @coversDefaultClass \App\ApiResource\Violation
 * @covers ::__construct
 * @covers ::jsonSerialize
 * @group apiResource
 * @group apiResource_violation
 */
final class ViolationTest extends TestCase
{
    // Methods :

    /**
     * Tests that the violation can be serialised.
     */
    public function testCanSerialiseViolation(): void
    {
        $violation = new Violation('property', 'message');

        $unserialisedViolation = json_decode(json_encode($violation));

        self::assertObjectHasAttribute('property', $unserialisedViolation);
        self::assertSame('property', $unserialisedViolation->property);
        self::assertObjectHasAttribute('message', $unserialisedViolation);
        self::assertSame('message', $unserialisedViolation->message);
    }
}
