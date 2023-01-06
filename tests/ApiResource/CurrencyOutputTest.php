<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\CurrencyOutput;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API currency output.
 *
 * @coversDefaultClass \App\ApiResource\CurrencyOutput
 * @covers ::__construct
 * @covers ::jsonSerialize
 * @group apiResource
 * @group apiResource_currencyOutput
 * @group currency
 */
final class CurrencyOutputTest extends TestCase
{
    // Methods :

    /**
     * Tests that the currency can be serialised.
     */
    public function testCanSerialiseCurrency(): void
    {
        $currencyOutput = new CurrencyOutput('EUR', 2);

        $unserialisedCurrency = json_decode(json_encode($currencyOutput));

        self::assertObjectHasAttribute('code', $unserialisedCurrency);
        self::assertSame('EUR', $unserialisedCurrency->code);
        self::assertObjectHasAttribute('decimals', $unserialisedCurrency);
        self::assertSame(2, $unserialisedCurrency->decimals);
    }
}
