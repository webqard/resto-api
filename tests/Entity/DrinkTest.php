<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Drink;
use App\Entity\Product;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Drink entity.
 */
#[
    PA\CoversClass(Drink::class),
    PA\CoversClass(Product::class),
    PA\Group('entities'),
    PA\Group('entities_drink'),
    PA\Group('drink')
]
final class DrinkTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $drink = new Drink();

        self::assertNull($drink->getId());
    }
}
