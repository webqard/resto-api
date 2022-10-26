<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Entity\Locale;
use PHPUnit\Framework\TestCase;

/**
 * Tests the CategoryTranslation entity.
 *
 * @coversDefaultClass \App\Entity\CategoryTranslation
 * @covers ::__construct
 * @uses \App\Entity\Category::__construct
 * @uses \App\Entity\Locale::__construct
 * @uses \App\Entity\ProductTranslation::__construct
 * @group entities
 * @group entities_categoryTranslation
 * @group categoryTranslation
 */
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
