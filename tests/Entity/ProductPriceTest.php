<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Currency;
use App\Entity\ProductPrice;
use PHPUnit\Framework\TestCase;

/**
 * Tests the ProductPrice entity.
 *
 * @coversDefaultClass \App\Entity\ProductPrice
 * @covers ::__construct
 * @uses \App\Entity\Currency::__construct
 * @group entities
 * @group entities_productPrice
 * @group productPrice
 */
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
