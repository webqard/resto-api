<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\CurrencyOutput;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API currency output.
 */
#[
    PA\CoversClass(CurrencyOutput::class),
    PA\Group('apiResource'),
    PA\Group('apiResource_currencyOutput'),
    PA\Group('currency')
]
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

        self::assertSame('EUR', $unserialisedCurrency->code);
        self::assertSame(2, $unserialisedCurrency->decimals);
    }
}
