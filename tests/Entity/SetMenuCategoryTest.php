<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SetMenu;
use App\Entity\SetMenuCategory;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuCategory entity.
 */
#[
    PA\CoversClass(SetMenuCategory::class),
    PA\UsesClass(Category::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(SetMenu::class),
    PA\Group('entities'),
    PA\Group('entities_setMenuCategory'),
    PA\Group('setMenuCategory')
]
final class SetMenuCategoryTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $setMenu = new SetMenu();
        $category = new Category();
        $setMenuCategory = new SetMenuCategory($setMenu, $category);

        self::assertNull($setMenuCategory->getId());
    }
}
