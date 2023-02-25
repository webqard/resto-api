<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Currency;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Currency entity.
 */
#[
    PA\CoversClass(Currency::class),
    PA\Group('entities'),
    PA\Group('entities_currency'),
    PA\Group('currency')
]
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
     * Test that the code can be returned and changed.
     */
    public function testCanGetAndSetCode(): void
    {
        $currency = new Currency('EUR', 2);

        self::assertSame('EUR', $currency->getCode());

        $currency->setCode('GBP');

        self::assertSame('GBP', $currency->getCode());
    }


    /**
     * Test that the decimals can be returned and changed.
     */
    public function testCanGetAndSetDecimals(): void
    {
        $currency = new Currency('EUR', 2);

        self::assertSame(2, $currency->getDecimals());

        $currency->setDecimals(3);

        self::assertSame(3, $currency->getDecimals());
    }
}
