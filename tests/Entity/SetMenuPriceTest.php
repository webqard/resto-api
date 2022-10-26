<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SetMenu;
use App\Entity\SetMenuPrice;
use App\Entity\Currency;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuPrice entity.
 *
 * @coversDefaultClass \App\Entity\SetMenuPrice
 * @covers ::__construct
 * @uses \App\Entity\SetMenu::__construct
 * @uses \App\Entity\Currency::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\ProductPrice::__construct
 * @group entities
 * @group entities_setMenuPrice
 * @group setMenuPrice
 */
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
