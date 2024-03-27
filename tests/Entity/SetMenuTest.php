<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Product;
use App\Entity\SetMenu;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenu entity.
 */
#[
    PA\CoversClass(Product::class),
    PA\CoversClass(SetMenu::class),
    PA\Group('entities'),
    PA\Group('entities_setMenu'),
    PA\Group('setMenu')
]
final class SetMenuTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $setMenu = new SetMenu();

        self::assertNull($setMenu->getId());
    }
}
