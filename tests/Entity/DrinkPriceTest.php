<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Drink;
use App\Entity\DrinkPrice;
use App\Entity\Currency;
use App\Entity\Product;
use App\Entity\ProductPrice;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the DrinkPrice entity.
 */
#[
    PA\CoversClass(DrinkPrice::class),
    PA\CoversClass(ProductPrice::class),
    PA\UsesClass(Drink::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(Product::class),
    PA\Group('entities'),
    PA\Group('entities_drinkPrice'),
    PA\Group('drinkPrice')
]
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
