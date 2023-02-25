<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Locale;
use App\Entity\ProductPictureTranslation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the ProductPictureTranslation entity.
 */
#[
    PA\CoversClass(ProductPictureTranslation::class),
    PA\UsesClass(Locale::class),
    PA\Group('entities'),
    PA\Group('entities_productPictureTranslation'),
    PA\Group('productPictureTranslation')
]
final class ProductPictureTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $locale = new Locale('en_GB');
        $productPictureTranslation = $this->getMockForAbstractClass(
            ProductPictureTranslation::class,
            [$locale]
        );

        self::assertNull($productPictureTranslation->getId());
    }
}
