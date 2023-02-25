<?php

declare(strict_types=1);

namespace App\Tests\State\Currency;

use App\ApiResource\CurrencyOutput;
use App\Entity\Currency;
use App\State\Currency\CurrencyProvider;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the currency provider.
 */
#[
    PA\CoversClass(CurrencyProvider::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(CurrencyOutput::class),
    PA\Group('state'),
    PA\Group('state_currencyProvider'),
    PA\Group('currency')
]
final class CurrencyProviderTest extends TestCase
{
    // Methods :

    /**
     * Test that the code can be returned.
     */
    public function testCanProvideCurrencyOutput(): void
    {
        $currency = new Currency('EUR', 2);
        $currencyProvider = new CurrencyProvider();
        $currencyOutput = $currencyProvider->provideCurrencyOutput($currency);

        self::assertInstanceOf(CurrencyOutput::class, $currencyOutput);

        $serialisedCurrencyOutput = $currencyOutput->jsonSerialize();

        self::assertIsArray($serialisedCurrencyOutput);
        self::assertArrayHasKey('code', $serialisedCurrencyOutput);
        self::assertSame('EUR', $serialisedCurrencyOutput['code']);
        self::assertArrayHasKey('decimals', $serialisedCurrencyOutput);
        self::assertSame(2, $serialisedCurrencyOutput['decimals']);
    }
}
