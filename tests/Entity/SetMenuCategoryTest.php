<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\SetMenu;
use App\Entity\SetMenuCategory;
use PHPUnit\Framework\TestCase;

/**
 * Tests the SetMenuCategory entity.
 *
 * @coversDefaultClass \App\Entity\SetMenuCategory
 * @covers ::__construct
 * @uses \App\Entity\Category::__construct
 * @uses \App\Entity\Product::__construct
 * @uses \App\Entity\SetMenu::__construct
 * @group entities
 * @group entities_setMenuCategory
 * @group setMenuCategory
 */
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
