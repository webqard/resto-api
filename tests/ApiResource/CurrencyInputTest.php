<?php

declare(strict_types=1);

namespace App\Tests\ApiResource;

use App\ApiResource\CurrencyInput;
use PHPUnit\Framework\TestCase;

/**
 * Tests the API currency input.
 *
 * @coversDefaultClass \App\ApiResource\CurrencyInput
 * @covers ::__construct
 * @group apiResource
 * @group apiResource_currencyInput
 * @group currency
 */
final class CurrencyInputTest extends TestCase
{
    // Methods :

    /**
     * Tests that the currency can return code.
     *
     * @covers ::getCode
     */
    public function testCanGetCode(): void
    {
        $currencyInput = new CurrencyInput('EUR', 2);

        self::assertSame('EUR', $currencyInput->getCode());
    }

    /**
     * Tests that the currency can return decimal.
     *
     * @covers ::getDecimals
     */
    public function testCanGetDecimals(): void
    {
        $currencyInput = new CurrencyInput('EUR', 2);

        self::assertSame(2, $currencyInput->getDecimals());
    }
}
