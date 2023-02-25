<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\Violation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API violation.
 */
#[
    PA\CoversClass(Violation::class),
    PA\Group('apiResource'),
    PA\Group('apiResource_violation')
]
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

        self::assertSame('property', $unserialisedViolation->property);
        self::assertSame('message', $unserialisedViolation->message);
    }
}
