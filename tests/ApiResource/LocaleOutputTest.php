<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\LocaleOutput;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API locale output.
 */
#[
    PA\CoversClass(LocaleOutput::class),
    PA\Group('apiResource'),
    PA\Group('apiResource_localeOutput'),
    PA\Group('locale')
]
final class LocaleOutputTest extends TestCase
{
    // Methods :

    /**
     * Tests that the locale can be serialised.
     */
    public function testCanSerialiseLocale(): void
    {
        $localeOutput = new LocaleOutput(1, 'en_GB');

        $unserialisedLocale = json_decode(json_encode($localeOutput));

        self::assertSame(1, $unserialisedLocale->id);
        self::assertSame('en_GB', $unserialisedLocale->code);
    }
}
