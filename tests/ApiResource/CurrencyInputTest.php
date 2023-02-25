<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\CurrencyInput;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API currency input.
 */
#[
    PA\CoversClass(CurrencyInput::class),
    PA\Group('apiResource'),
    PA\Group('apiResource_currencyInput'),
    PA\Group('currency')
]
final class CurrencyInputTest extends TestCase
{
    // Methods :

    /**
     * Tests that the currency can return code.
     */
    public function testCanGetCode(): void
    {
        $currencyInput = new CurrencyInput('EUR', 2);

        self::assertSame('EUR', $currencyInput->getCode());
    }

    /**
     * Tests that the currency can return decimal.
     */
    public function testCanGetDecimals(): void
    {
        $currencyInput = new CurrencyInput('EUR', 2);

        self::assertSame(2, $currencyInput->getDecimals());
    }
}
