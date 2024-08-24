<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\RoleOutput;
use App\ApiResource\RoleTranslationOutput;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API role output.
 */
#[
    PA\CoversClass(RoleOutput::class),
    PA\UsesClass(RoleTranslationOutput::class),
    PA\Group('apiResource'),
    PA\Group('apiResource_roleOutput'),
    PA\Group('role')
]
final class RoleOutputTest extends TestCase
{
    // Methods :

    /**
     * Tests that the role translation can be serialised
     * without translation.
     */
    public function testCanSerialiseRoleWithoutTranslation(): void
    {
        $roleOutput = new RoleOutput(1, 'name');

        $unserialisedRole = json_decode(json_encode($roleOutput));

        self::assertSame(1, $unserialisedRole->id);
        self::assertSame('name', $unserialisedRole->name);
    }


    /**
     * Tests that the role can be serialised
     * with a translation.
     */
    public function testCanSerialiseRoleWithATranslation(): void
    {
        $roleENTranslationOutput = new RoleTranslationOutput(1, 'en-description', 1);
        $roleOutput = new RoleOutput(1, 'name', [
            $roleENTranslationOutput
        ]);

        $unserialisedRole = json_decode(json_encode($roleOutput));

        self::assertSame(1, $unserialisedRole->id);
        self::assertSame('name', $unserialisedRole->name);

        self::assertCount(1, $unserialisedRole->translations);
        self::assertArrayHasKey(0, $unserialisedRole->translations);

        $enTranslation = $unserialisedRole->translations[0];
        self::assertSame(1, $enTranslation->id);
        self::assertSame('en-description', $enTranslation->description);
        self::assertSame(1, $enTranslation->localeId);
    }


    /**
     * Tests that the role can be serialised
     * with many translations.
     */
    public function testCanSerialiseRoleWithManyTranslations(): void
    {
        $roleENTranslationOutput = new RoleTranslationOutput(1, 'en-description', 1);
        $roleFRTranslationOutput = new RoleTranslationOutput(2, 'fr-description', 2);
        $roleOutput = new RoleOutput(1, 'name', [
            $roleENTranslationOutput,
            $roleFRTranslationOutput
        ]);

        $unserialisedRole = json_decode(json_encode($roleOutput));

        self::assertSame(1, $unserialisedRole->id);
        self::assertSame('name', $unserialisedRole->name);

        self::assertCount(2, $unserialisedRole->translations);
        self::assertArrayHasKey(0, $unserialisedRole->translations);
        self::assertArrayHasKey(1, $unserialisedRole->translations);

        $enTranslation = $unserialisedRole->translations[0];
        self::assertSame(1, $enTranslation->id);
        self::assertSame('en-description', $enTranslation->description);
        self::assertSame(1, $enTranslation->localeId);

        $frTranslation = $unserialisedRole->translations[1];
        self::assertSame(2, $frTranslation->id);
        self::assertSame('fr-description', $frTranslation->description);
        self::assertSame(2, $frTranslation->localeId);
    }
}
