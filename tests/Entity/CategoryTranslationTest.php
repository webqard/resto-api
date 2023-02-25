<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Entity\Locale;
use App\Entity\ProductTranslation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the CategoryTranslation entity.
 */
#[
    PA\CoversClass(CategoryTranslation::class),
    PA\UsesClass(Category::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(ProductTranslation::class),
    PA\Group('entities'),
    PA\Group('entities_categoryTranslation'),
    PA\Group('categoryTranslation')
]
final class CategoryTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $category = new Category();
        $locale = new Locale('en_GB');
        $categoryTranslation = new CategoryTranslation(
            $category,
            'test-name',
            'test-slug',
            $locale
        );

        self::assertNull($categoryTranslation->getId());
    }
}
