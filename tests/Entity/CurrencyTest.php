<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Currency;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Currency entity.
 *
 * @coversDefaultClass \App\Entity\Currency
 * @covers ::__construct
 * @group entities
 * @group entities_currency
 * @group currency
 */
final class CurrencyTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $currency = new Currency('EUR', 2);

        self::assertNull($currency->getId());
    }


    /**
     * Test that the code can be returned.
     *
     * @covers ::getCode
     */
    public function testCanGetCode(): void
    {
        $currency = new Currency('EUR', 2);

        self::assertSame('EUR', $currency->getCode());
    }


    /**
     * Test that the decimals can be returned.
     *
     * @covers ::getDecimals
     */
    public function testCanGetDecimals(): void
    {
        $currency = new Currency('EUR', 2);

        self::assertSame(2, $currency->getDecimals());
    }
}
