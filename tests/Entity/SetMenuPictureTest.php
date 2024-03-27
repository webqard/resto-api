<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Product;
use App\Entity\ProductPicture;
use App\Entity\SetMenu;
use App\Entity\SetMenuPicture;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuPicture entity.
 */
#[
    PA\CoversClass(ProductPicture::class),
    PA\CoversClass(SetMenuPicture::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(SetMenu::class),
    PA\Group('entities'),
    PA\Group('entities_setMenuPicture'),
    PA\Group('setMenuPicture')
]
final class SetMenuPictureTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $setMenu = new SetMenu();
        $setMenuPicture = new SetMenuPicture(
            $setMenu,
            'test-source',
        );

        self::assertNull($setMenuPicture->getId());
    }
}
