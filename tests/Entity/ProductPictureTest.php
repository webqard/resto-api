<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ProductPicture;
use PHPUnit\Framework\TestCase;

/**
 * Tests the ProductPicture entity.
 *
 * @coversDefaultClass \App\Entity\ProductPicture
 * @covers ::__construct
 * @group entities
 * @group entities_productPicture
 * @group productPicture
 */
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
