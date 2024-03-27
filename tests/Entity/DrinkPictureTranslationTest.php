<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Drink;
use App\Entity\DrinkPicture;
use App\Entity\DrinkPictureTranslation;
use App\Entity\Locale;
use App\Entity\Product;
use App\Entity\ProductPicture;
use App\Entity\ProductPictureTranslation;
use PHPUnit\Framework\Attributes as PA;
use PHPUnit\Framework\TestCase;

/**
 * Tests the DrinkPictureTranslation entity.
 */
#[
    PA\CoversClass(DrinkPictureTranslation::class),
    PA\CoversClass(ProductPictureTranslation::class),
    PA\UsesClass(Drink::class),
    PA\UsesClass(DrinkPicture::class),
    PA\UsesClass(Locale::class),
    PA\UsesClass(Product::class),
    PA\UsesClass(ProductPicture::class),
    PA\Group('entities'),
    PA\Group('entities_drinkPictureTranslation'),
    PA\Group('drinkPictureTranslation')
]
final class DrinkPictureTranslationTest extends TestCase
{
    // Methods :

    /**
     * Test that the identifier
     * is initialised to null.
     */
    public function testCanInitialiseIdentifierToNull(): void
    {
        $drink = new Drink();
        $drinkPicture = new DrinkPicture($drink, 'test-source');
        $locale = new Locale('en_GB');
        $drinkPictureTranslation = new DrinkPictureTranslation(
            $drinkPicture,
            $locale
        );

        self::assertNull($drinkPictureTranslation->getId());
    }
}
