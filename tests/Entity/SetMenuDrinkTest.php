<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Drink;
use App\Entity\SetMenu;
use App\Entity\SetMenuDrink;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuDrink entity.
 *
 * @coversDefaultClass \App\Entity\SetMenuDrink
 * @covers ::__construct
 * @uses \App\Entity\Drink::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\SetMenu::__construct
 * @uses \App\Entity\SetMenuProduct::__construct
 * @group entities
 * @group entities_setMenuDrink
 * @group setMenuDrink
 */
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
