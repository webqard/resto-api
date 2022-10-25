<?php

declare(strict_types=1);

namespace App\Tests\State;

use App\ApiResource\CurrencyOutput;
use App\Entity\Currency;
use App\State\CurrencyProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the currency provider.
 *
 * @coversDefaultClass \App\State\CurrencyProvider
 * @covers ::provideCurrencyOutput
 * @group state
 * @group state_currencyProvider
 * @group currency
 */
final class CurrencyProviderTest extends TestCase
{
    // Methods :

    /**
     * Test that the code can be returned.
     *
     * @uses \App\ApiResource\CurrencyOutput::__construct
     * @uses \App\ApiResource\CurrencyOutput::jsonSerialize
     * @uses \App\Entity\Currency::__construct
     * @uses \App\Entity\Currency::getCode
     * @uses \App\Entity\Currency::getDecimals
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
