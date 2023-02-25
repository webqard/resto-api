<?php

declare(strict_types=1);

namespace App\Tests\State\Locale;

use App\ApiResource\LocaleOutput;
use App\Entity\Locale;
use App\State\Locale\LocaleProvider;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the locale provider.
 */
#[
    PA\CoversClass(LocaleProvider::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(LocaleOutput::class),
    PA\Group('state'),
    PA\Group('state_localeProvider'),
    PA\Group('locale')
]
final class LocaleProviderTest extends TestCase
{
    // Methods :

    /**
     * Test that the code can be returned.
     */
    public function testCanGetProvideLocaleOutput(): void
    {
        $locale = new Locale('en_GB');
        $localeProvider = new LocaleProvider();
        $localeOutput = $localeProvider->provideLocaleOutput($locale);

        self::assertInstanceOf(LocaleOutput::class, $localeOutput);

        $serialisedLocaleOutput = $localeOutput->jsonSerialize();

        self::assertIsArray($serialisedLocaleOutput);
        self::assertArrayHasKey('code', $serialisedLocaleOutput);
        self::assertSame('en_GB', $serialisedLocaleOutput['code']);
    }
}
