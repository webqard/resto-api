<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Drink;
use App\Entity\DrinkPicture;
use App\Entity\Product;
use App\Entity\ProductPicture;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the DrinkPicture entity.
 */
#[
    PA\CoversClass(DrinkPicture::class),
    PA\CoversClass(ProductPicture::class),
    PA\UsesClass(Drink::class),
    PA\UsesClass(Product::class),
    PA\Group('entities'),
    PA\Group('entities_drinkPicture'),
    PA\Group('drinkPicture')
]
final class DrinkPictureTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $drink = new Drink();
        $drinkPicture = new DrinkPicture(
            $drink,
            'test-source',
        );

        self::assertNull($drinkPicture->getId());
    }
}
