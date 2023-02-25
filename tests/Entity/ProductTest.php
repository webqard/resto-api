<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Product entity.
 */
#[
    PA\CoversClass(Product::class),
    PA\Group('entities'),
    PA\Group('entities_product'),
    PA\Group('product')
]
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
