<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Product;
use App\Entity\SetMenu;
use App\Entity\SetMenuProduct;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuProduct entity.
 */
#[
    PA\CoversClass(SetMenuProduct::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(SetMenu::class),
    PA\Group('entities'),
    PA\Group('entities_setMenuProduct'),
    PA\Group('setMenuProduct')
]
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
