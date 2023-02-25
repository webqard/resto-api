<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Currency;
use App\Entity\Product;
use App\Entity\ProductPrice;
use App\Entity\SetMenu;
use App\Entity\SetMenuPrice;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuPrice entity.
 */
#[
    PA\CoversClass(SetMenuPrice::class),
    PA\UsesClass(Currency::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(ProductPrice::class),
    PA\UsesClass(SetMenu::class),
    PA\Group('entities'),
    PA\Group('entities_setMenuPrice'),
    PA\Group('setMenuPrice')
]
final class SetMenuPriceTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $setMenu = new SetMenu();
        $currency = new Currency('EUR', 2);
        $setMenuPrice = new SetMenuPrice(
            $setMenu,
            5,
            $currency,
            new \DateTimeImmutable()
        );

        self::assertNull($setMenuPrice->getId());
    }
}
