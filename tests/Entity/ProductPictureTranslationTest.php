<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Locale;
use App\Entity\ProductPictureTranslation;
use PHPUnit\Framework\TestCase;

/**
 * Tests the ProductPictureTranslation entity.
 *
 * @coversDefaultClass \App\Entity\ProductPictureTranslation
 * @covers ::__construct
 * @uses \App\Entity\Locale::__construct
 * @group entities
 * @group entities_productPictureTranslation
 * @group productPictureTranslation
 */
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
