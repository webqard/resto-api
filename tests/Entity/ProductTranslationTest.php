<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Locale;
use App\Entity\ProductTranslation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the ProductTranslation entity.
 */
#[
    PA\CoversClass(ProductTranslation::class),
    PA\UsesClass(Locale::class),
    PA\Group('entities'),
    PA\Group('entities_productTranslation'),
    PA\Group('productTranslation')
]
final class ProductTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $locale = new Locale('en_GB');
        $productTranslation = $this->getMockForAbstractClass(
            ProductTranslation::class,
            [
                'test-name',
                'test-slug',
                $locale
            ]
        );

        self::assertNull($productTranslation->getId());
    }
}
