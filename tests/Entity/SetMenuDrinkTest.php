<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Drink;
use App\Entity\Product;
use App\Entity\SetMenu;
use App\Entity\SetMenuDrink;
use App\Entity\SetMenuProduct;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuDrink entity.
 */
#[
    PA\CoversClass(SetMenuDrink::class),
    PA\CoversClass(SetMenuProduct::class),
    PA\UsesClass(Drink::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(SetMenu::class),
    PA\Group('entities'),
    PA\Group('entities_setMenuDrink'),
    PA\Group('setMenuDrink')
]
final class SetMenuDrinkTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $drink = new Drink();
        $setMenu = new SetMenu();
        $setMenuDrink = new SetMenuDrink($drink, $setMenu);

        self::assertNull($setMenuDrink->getId());
    }
}
