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
     * Test that a DomainException is thrown
     * if the entity is not persisted.
     */
    public function testCanThrowADomainExceptionIfTheEntityIsNotPersisted(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('entityNotPersisted');

        $locale = new Locale('en_GB');
        $localeProvider = new LocaleProvider();
        $localeProvider->provideLocaleOutput($locale);
    }


    /**
     * Test that the locale output can be provided.
     */
    public function testCanProvideLocaleOutput(): void
    {
        $locale = $this->createStub(Locale::class);
        $locale->method('getId')
            ->willReturn(1);
        $locale->method('getCode')
            ->willReturn('en_GB');

        $localeProvider = new LocaleProvider();
        $localeOutput = $localeProvider->provideLocaleOutput($locale);

        $serialisedLocaleOutput = $localeOutput->jsonSerialize();

        self::assertIsArray($serialisedLocaleOutput);
        self::assertArrayHasKey('id', $serialisedLocaleOutput);
        self::assertSame(1, $serialisedLocaleOutput['id']);
        self::assertArrayHasKey('code', $serialisedLocaleOutput);
        self::assertSame('en_GB', $serialisedLocaleOutput['code']);
    }
}
