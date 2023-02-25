<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Currency;
use App\Entity\ProductPrice;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the ProductPrice entity.
 */
#[
    PA\CoversClass(ProductPrice::class),
    PA\UsesClass(Currency::class),
    PA\Group('entities'),
    PA\Group('entities_productPrice'),
    PA\Group('productPrice')
]
final class ProductPriceTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $currency = new Currency('EUR', 2);
        $productPrice = $this->getMockForAbstractClass(
            ProductPrice::class,
            [
                5,
                $currency,
                new \DateTimeImmutable()
            ]
        );

        self::assertNull($productPrice->getId());
    }
}
