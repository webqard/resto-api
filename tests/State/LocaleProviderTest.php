<?php

declare(strict_types=1);

namespace App\Tests\State;

use App\ApiResource\LocaleOutput;
use App\Entity\Locale;
use App\State\LocaleProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the locale provider.
 *
 * @coversDefaultClass \App\State\LocaleProvider
 * @covers ::provideLocaleOutput
 * @group state
 * @group state_localeProvider
 * @group locale
 */
final class LocaleProviderTest extends TestCase
{
    // Methods :

    /**
     * Test that the code can be returned.
     *
     * @uses \App\ApiResource\LocaleOutput::__construct
     * @uses \App\ApiResource\LocaleOutput::jsonSerialize
     * @uses \App\Entity\Locale::__construct
     * @uses \App\Entity\Locale::getCode
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
