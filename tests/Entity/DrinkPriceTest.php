<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Drink;
use App\Entity\DrinkPrice;
use App\Entity\Currency;
use PHPUnit\Framework\TestCase;

/**
 * Tests the DrinkPrice entity.
 *
 * @coversDefaultClass \App\Entity\DrinkPrice
 * @covers ::__construct
 * @uses \App\Entity\Drink::__construct
 * @uses \App\Entity\Currency::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\ProductPrice::__construct
 * @group entities
 * @group entities_drinkPrice
 * @group drinkPrice
 */
final class DrinkPriceTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $drink = new Drink();
        $currency = new Currency('EUR', 2);
        $drinkPrice = new DrinkPrice(
            $drink,
            5,
            250,
            $currency,
            new \DateTimeImmutable()
        );

        self::assertNull($drinkPrice->getId());
    }
}
