<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Product entity.
 *
 * @coversDefaultClass \App\Entity\Product
 * @covers ::__construct
 * @group entities
 * @group entities_product
 * @group product
 */
final class ProductTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $product = $this->getMockForAbstractClass(Product::class);

        self::assertNull($product->getId());
    }
}
