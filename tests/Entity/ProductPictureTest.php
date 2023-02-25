<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ProductPicture;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the ProductPicture entity.
 */
#[
    PA\CoversClass(ProductPicture::class),
    PA\Group('entities'),
    PA\Group('entities_productPicture'),
    PA\Group('productPicture')
]
final class ProductPictureTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $productPicture = $this->getMockForAbstractClass(
            ProductPicture::class,
            ['test-source']
        );

        self::assertNull($productPicture->getId());
    }
}
