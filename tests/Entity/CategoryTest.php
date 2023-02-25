<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Category;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Category entity.
 */
#[
    PA\CoversClass(Category::class),
    PA\Group('entities'),
    PA\Group('entities_category'),
    PA\Group('category')
]
final class CategoryTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $category = new Category();

        self::assertNull($category->getId());
    }
}
