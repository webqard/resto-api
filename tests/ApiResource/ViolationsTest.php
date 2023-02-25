<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\Violation;
use App\ApiResource\Violations;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API violations.
 */
#[
    PA\CoversClass(Violations::class),
    PA\UsesClass(Violation::class),
    PA\Group('apiResource'),
    PA\Group('apiResource_violations')
]
final class ViolationsTest extends TestCase
{
    // Methods :

    /**
     * Tests that the violations can be serialised.
     */
    public function testCanSerialiseViolations(): void
    {
        $violations = new Violations([
            new Violation('property', 'message'),
            new Violation('property2', 'message2')
        ]);

        $unserialisedViolations = json_decode(json_encode($violations));

        self::assertIsArray($unserialisedViolations);

        self::assertArrayHasKey(0, $unserialisedViolations);
        self::assertSame('property', $unserialisedViolations[0]->property);
        self::assertSame('message', $unserialisedViolations[0]->message);

        self::assertArrayHasKey(1, $unserialisedViolations);
        self::assertSame('property2', $unserialisedViolations[1]->property);
        self::assertSame('message2', $unserialisedViolations[1]->message);
    }

    /**
     * Tests that the violations can be serialised
     * when the list is empty.
     */
    public function testCanSerialiseAnEmptyViolations(): void
    {
        $violations = new Violations();

        $unserialisedViolations = json_decode(json_encode($violations));

        self::assertIsArray($unserialisedViolations);
        self::assertCount(0, $unserialisedViolations);
    }

    /**
     * Tests that a violation can be added
     * to an empty list.
     */
    public function testCanAddAViolationToAnEmptyList(): void
    {
        $violations = new Violations();

        $unserialisedEmptyViolations = json_decode(json_encode($violations));

        self::assertIsArray($unserialisedEmptyViolations);
        self::assertCount(0, $unserialisedEmptyViolations);

        $violations->add(new Violation('property', 'message'));

        $unserialisedViolations = json_decode(json_encode($violations));

        self::assertIsArray($unserialisedViolations);
        self::assertCount(1, $unserialisedViolations);

        self::assertArrayHasKey(0, $unserialisedViolations);
        self::assertSame('property', $unserialisedViolations[0]->property);
        self::assertSame('message', $unserialisedViolations[0]->message);
    }

    /**
     * Tests that a violation can be added
     * to a non empty list.
     */
    public function testCanAddAViolationToANonEmptyList(): void
    {
        $violations = new Violations([new Violation('property', 'message')]);
        $violations->add(new Violation('property2', 'message2'));

        $unserialisedViolations = json_decode(json_encode($violations));

        self::assertIsArray($unserialisedViolations);
        self::assertCount(2, $unserialisedViolations);

        self::assertArrayHasKey(0, $unserialisedViolations);
        self::assertSame('property', $unserialisedViolations[0]->property);
        self::assertSame('message', $unserialisedViolations[0]->message);

        self::assertArrayHasKey(1, $unserialisedViolations);
        self::assertSame('property2', $unserialisedViolations[1]->property);
        self::assertSame('message2', $unserialisedViolations[1]->message);
    }
}
