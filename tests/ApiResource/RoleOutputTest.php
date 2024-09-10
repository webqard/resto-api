<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\LocaleOutput;
use App\ApiResource\RoleOutput;
use App\ApiResource\RoleTranslationOutput;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API role output.
 */
#[
    PA\CoversClass(RoleOutput::class),
    PA\UsesClass(LocaleOutput::class),
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
    public function testCanBeSerialisedWithoutTranslation(): void
    {
        $roleOutput = new RoleOutput(1, 'name');

        $unserialisedRole = json_decode(json_encode($roleOutput), true);

        self::assertArrayHasKey('id', $unserialisedRole);
        self::assertSame(1, $unserialisedRole['id']);
        self::assertArrayHasKey('name', $unserialisedRole);
        self::assertSame('name', $unserialisedRole['name']);
        self::assertArrayNotHasKey('translations', $unserialisedRole);
    }


    /**
     * Tests that the role can be serialised
     * with a translation.
     */
    public function testCanBeSerialisedWithATranslation(): void
    {
        $localeOutputEN = new LocaleOutput(1, 'en_GB');
        $roleTranslationOutputEN = new RoleTranslationOutput(1, 'en-description', $localeOutputEN);
        $roleOutput = new RoleOutput(1, 'name', [
            $roleTranslationOutputEN
        ]);

        $unserialisedRole = json_decode(json_encode($roleOutput), false);

        self::assertSame(1, $unserialisedRole->id);
        self::assertSame('name', $unserialisedRole->name);
        self::assertCount(1, $unserialisedRole->translations);

        $enTranslation = $unserialisedRole->translations[0];
        self::assertSame(1, $enTranslation->id);
        self::assertSame('en-description', $enTranslation->description);
        self::assertSame(1, $enTranslation->locale->id);
        self::assertSame('en_GB', $enTranslation->locale->code);
    }


    /**
     * Tests that the role can be serialised
     * with many translations.
     */
    public function testCanBeSerialisedWithManyTranslations(): void
    {
        $localeOutputEN = new LocaleOutput(1, 'en_GB');
        $localeOutputFR = new LocaleOutput(2, 'fr_FR');
        $roleTranslationOutputEN = new RoleTranslationOutput(1, 'en-description', $localeOutputEN);
        $roleTranslationOutputFR = new RoleTranslationOutput(2, 'fr-description', $localeOutputFR);
        $roleOutput = new RoleOutput(1, 'name', [
            $roleTranslationOutputEN,
            $roleTranslationOutputFR
        ]);

        $unserialisedRole = json_decode(json_encode($roleOutput), false);

        self::assertSame(1, $unserialisedRole->id);
        self::assertSame('name', $unserialisedRole->name);
        self::assertCount(2, $unserialisedRole->translations);

        $enTranslation = $unserialisedRole->translations[0];
        self::assertSame(1, $enTranslation->id);
        self::assertSame('en-description', $enTranslation->description);
        self::assertSame(1, $enTranslation->locale->id);
        self::assertSame('en_GB', $enTranslation->locale->code);

        $frTranslation = $unserialisedRole->translations[1];
        self::assertSame(2, $frTranslation->id);
        self::assertSame('fr-description', $frTranslation->description);
        self::assertSame(2, $frTranslation->locale->id);
        self::assertSame('fr_FR', $frTranslation->locale->code);
    }
}
