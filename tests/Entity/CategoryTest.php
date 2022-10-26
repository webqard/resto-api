<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Category entity.
 *
 * @coversDefaultClass \App\Entity\Category
 * @covers ::__construct
 * @group entities
 * @group entities_category
 * @group category
 */
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
