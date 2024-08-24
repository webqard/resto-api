<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\RoleTranslationOutput;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API role translation output.
 */
#[
    PA\CoversClass(RoleTranslationOutput::class),
    PA\Group('apiResource'),
    PA\Group('apiResource_roleTranslationOutput'),
    PA\Group('role')
]
final class RoleTranslationOutputTest extends TestCase
{
    // Methods :

    /**
     * Tests that the role translation can be serialised.
     */
    public function testCanSerialiseRoleTranslation(): void
    {
        $roleTranslationOutput = new RoleTranslationOutput(1, 'description', 1);

        $unserialisedTranslation = json_decode(json_encode($roleTranslationOutput));

        self::assertSame(1, $unserialisedTranslation->id);
        self::assertSame('description', $unserialisedTranslation->description);
        self::assertSame(1, $unserialisedTranslation->localeId);
    }
}
