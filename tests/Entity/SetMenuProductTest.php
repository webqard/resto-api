<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SetMenu;
use App\Entity\SetMenuProduct;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuProduct entity.
 *
 * @coversDefaultClass \App\Entity\SetMenuProduct
 * @covers ::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\SetMenu::__construct
 * @group entities
 * @group entities_setMenuProduct
 * @group setMenuProduct
 */
final class SetMenuProductTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $setMenu = new SetMenu();
        $setMenuProduct = $this->getMockForAbstractClass(
            SetMenuProduct::class,
            [$setMenu]
        );

        self::assertNull($setMenuProduct->getId());
    }
}
